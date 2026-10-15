<?php
declare(strict_types=1);

namespace Request;

use Http\Request;
use Http\Response;

/** Introduction-document requests share the existing console request aggregate. */
final class BrochureRequestService
{
    private \PDO $db;

    public function __construct(\PDO $db) { $this->db = $db; }

    public static function token(): string
    {
        if (empty($_SESSION['brochure_csrf'])) {
            $_SESSION['brochure_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['brochure_csrf'];
    }

    private static function enabled(): bool
    {
        return project_feature_enabled('content.brochure_request', false);
    }

    public function submit(Request $request): Response
    {
        if (!self::enabled()) return $this->json(['success' => false], 404);
        if (!$request->isMethod('POST')) return $this->json(['success' => false], 405);
        $csrf = $request->post('_token', '');
        if (!is_string($csrf) || !hash_equals(self::token(), $csrf)) {
            return $this->json(['success' => false, 'message' => '페이지를 새로고침한 뒤 다시 신청해 주세요.'], 403);
        }
        $data = [];
        $errors = [];
        foreach (['company' => 100, 'name' => 100, 'phone' => 30, 'email' => 190, 'message' => 2000] as $field => $max) {
            $value = $request->post($field, '');
            $data[$field] = is_string($value) ? trim($value) : '';
            if (!is_string($value) || mb_strlen($data[$field]) > $max || ($field !== 'message' && $data[$field] === '')) {
                $errors[$field] = '필수 항목과 입력 길이를 확인해 주세요.';
            }
        }
        if (!preg_match('/^\+?[0-9() .-]{9,30}$/D', $data['phone']) || strlen(preg_replace('/\D/', '', $data['phone'])) < 9) {
            $errors['phone'] = '연락처를 정확히 입력해 주세요.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = '이메일 주소를 확인해 주세요.';
        if ($request->post('privacy_consent') !== '1') $errors['privacy_consent'] = '개인정보 수집·이용에 동의해 주세요.';
        $key = $request->post('submission_key', '');
        if (!is_string($key) || !preg_match('/^[a-f0-9]{32}$/D', $key)) $errors['submission_key'] = '신청 양식을 다시 열어 주세요.';
        if ($request->post('website', '') !== '') $errors['website'] = '요청을 처리할 수 없습니다.';
        if ($errors) return $this->json(['success' => false, 'message' => reset($errors), 'errors' => $errors], 422);

        $file = ROOT_PATH . '/' . \ProjectHelper::config('brochure.file', '');
        if (!is_file($file) || !is_readable($file)) return $this->json(['success' => false, 'message' => '소개서가 준비 중입니다. 잠시 후 다시 시도해 주세요.'], 503);
        // Session-bound unique key prevents double clicks and network retries inserting twice.
        $submission = hash('sha256', self::token() . $key);
        try {
            $existing = $this->db->prepare('SELECT no FROM nb_request WHERE submission_key = :key AND sitekey = :sitekey');
            $existing->execute(['key' => $submission, 'sitekey' => NB_SITEKEY]);
            if (!$existing->fetchColumn()) {
                if (time() - (int) ($_SESSION['brochure_last_submit'] ?? 0) < 10) {
                    return $this->json(['success' => false, 'message' => '잠시 후 다시 신청해 주세요.'], 429);
                }
                $ip = (string) $request->server('REMOTE_ADDR', '');
                $limit = $this->db->prepare("SELECT COUNT(*) FROM nb_request WHERE sitekey = :sitekey AND request_type = 'brochure_download' AND ip_address = :ip AND regdate > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
                $limit->execute(['sitekey' => NB_SITEKEY, 'ip' => $ip]);
                if ((int) $limit->fetchColumn() >= (int) \ProjectHelper::config('brochure.hourly_limit', 5)) {
                    return $this->json(['success' => false, 'message' => '신청 횟수를 초과했습니다. 잠시 후 다시 시도해 주세요.'], 429);
                }
                $stmt = $this->db->prepare("INSERT INTO nb_request (sitekey, request_type, company, name, phone, email, message, gender, residence, interest_codes, privacy_consent, consent_version, agreed_at, submission_key, ip_address, user_agent) VALUES (:sitekey, 'brochure_download', :company, :name, :phone, :email, :message, 0, 0, '[]', 1, :version, NOW(), :key, :ip, :agent)");
                $stmt->execute([
                    'sitekey' => NB_SITEKEY, 'company' => $data['company'], 'name' => $data['name'],
                    'phone' => $data['phone'], 'email' => $data['email'], 'message' => $data['message'],
                    'version' => \ProjectHelper::config('brochure.consent_version', '20261007'), 'key' => $submission,
                    'ip' => $ip, 'agent' => mb_substr((string) $request->header('User-Agent', ''), 0, 255),
                ]);
                $_SESSION['brochure_last_submit'] = time();
            }
        } catch (\Throwable $e) {
            // Do not expose SQL or personal information to clients/logs.
            error_log('[brochure-request] storage failed: ' . get_class($e));
            return $this->json(['success' => false, 'message' => '신청을 저장하지 못했습니다. 잠시 후 다시 시도해 주세요.'], 503);
        }
        $_SESSION['brochure_download_token'] = $_SESSION['brochure_download_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['brochure_download_expires'] = time() + 1800;
        return $this->json(['success' => true, 'message' => '신청이 접수되었습니다. 소개서를 다운로드해 주세요.', 'download_url' => base_path('/brochure/download') . '?token=' . $_SESSION['brochure_download_token']], 201);
    }

    public static function download(Request $request): Response
    {
        $token = $request->query('token', '');
        if (!self::enabled() || !is_string($token) || empty($_SESSION['brochure_download_token']) || !hash_equals($_SESSION['brochure_download_token'], $token) || (int) ($_SESSION['brochure_download_expires'] ?? 0) < time()) {
            return response('소개서 신청 후 다운로드해 주세요.', 403, ['Content-Type' => 'text/plain; charset=utf-8', 'Cache-Control' => 'no-store']);
        }
        $file = ROOT_PATH . '/' . \ProjectHelper::config('brochure.file', '');
        if (!is_file($file) || !is_readable($file)) return response('소개서가 준비 중입니다.', 503);
        return response((string) file_get_contents($file), 200, [
            'Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="partnerit-service-brief.pdf"',
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function json(array $data, int $status): Response
    {
        return response()->json($data, $status)->header('Cache-Control', 'no-store');
    }
}
