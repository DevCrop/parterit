<?php

declare(strict_types=1);

namespace Request;

use Database\DB;
use Http\Request;
use Http\Response;
use Mail\MailerInterface;

final class AssetRequestService
{
    private \PDO $db;
    private MailerInterface $mailer;
    private string $notifyTo;
    private array $options;
    private string $formTitle;
    private string $requestType;

    public function __construct(
        \PDO $db,
        MailerInterface $mailer,
        string $notifyTo,
        array $options,
        string $formTitle = '문의 신청이 접수되었습니다',
        string $requestType = 'contact_request'
    ) {
        $this->db          = $db;
        $this->mailer      = $mailer;
        $this->notifyTo    = $notifyTo;
        $this->options     = $options;
        $this->formTitle   = $formTitle;
        $this->requestType = $requestType;
    }

    public function submit(Request $request): array
    {
        if (!$request->isMethod('POST')) {
            return ['success' => false, 'status' => 405, 'message' => 'Invalid request method.'];
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if ($errors !== []) {
            return ['success' => false, 'status' => 422, 'message' => '입력값을 다시 확인해 주세요.', 'errors' => $errors];
        }

        $id = $this->insert($data, $request);

        if ($this->notifyTo !== '') {
            $this->mailer->send(
                $this->notifyTo,
                $this->formTitle,
                $this->buildNotificationHtml($data)
            );
        }

        return ['success' => true, 'status' => 201, 'message' => $this->formTitle . '.', 'id' => $id];
    }

    public function response(Request $request): Response
    {
        $result     = $this->submit($request);
        $wantsJson  = $request->isAjax()
            || strpos(strtolower((string) $request->header('Accept', '')), 'application/json') !== false;

        if ($wantsJson) {
            return response()->json($result, (int) ($result['status'] ?? 200));
        }

        return redirect(route('contact'), 302);
    }

    private function extractData(Request $request): array
    {
        $interestInput = $request->post('interests', []);
        if (!is_array($interestInput)) {
            $interestInput = [$interestInput];
        }

        $interestCodes = array_values(array_unique(array_map('intval', $interestInput)));
        $interestCodes = array_values(array_filter($interestCodes, function (int $code): bool {
            return isset($this->options['interests'][$code]);
        }));

        return [
            'name'           => trim((string) $request->post('name', '')),
            'phone'          => trim((string) $request->post('phone', '')),
            'phone_digits'   => preg_replace('/\D+/', '', trim((string) $request->post('phone', ''))) ?? '',
            'gender'         => (int) $request->post('gender', 0),
            'residence'      => (int) $request->post('residence', 0),
            'residence_other'=> trim((string) $request->post('residence_other', '')),
            'interest_codes' => $interestCodes,
            'ip_address'     => $request->ip(),
            'user_agent'     => mb_substr((string) $request->header('User-Agent', ''), 0, 255),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = '이름을 입력해 주세요.';
        }

        if ($data['phone'] === '' || strlen($data['phone_digits']) < 9) {
            $errors['phone'] = '연락처를 정확히 입력해 주세요.';
        }

        if (!isset($this->options['gender'][$data['gender']])) {
            $errors['gender'] = '성별을 선택해 주세요.';
        }

        if (!isset($this->options['residence'][$data['residence']])) {
            $errors['residence'] = '거주지를 선택해 주세요.';
        }

        if ($data['residence'] === 8 && $data['residence_other'] === '') {
            $errors['residence_other'] = '기타 지역을 입력해 주세요.';
        }

        if ($data['interest_codes'] === []) {
            $errors['interests'] = '관심분야를 하나 이상 선택해 주세요.';
        }

        return $errors;
    }

    private function insert(array $data, Request $request): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO nb_request (
                sitekey, request_type, name, phone, gender, residence,
                residence_other, interest_codes, ip_address, user_agent, regdate
            ) VALUES (
                :sitekey, :request_type, :name, :phone, :gender, :residence,
                :residence_other, :interest_codes, :ip_address, :user_agent, NOW()
            )
        ');

        $stmt->execute([
            ':sitekey'          => defined('NB_SITEKEY') ? NB_SITEKEY : '',
            ':request_type'     => $this->requestType,
            ':name'             => $data['name'],
            ':phone'            => $data['phone'],
            ':gender'           => $data['gender'],
            ':residence'        => $data['residence'],
            ':residence_other'  => $data['residence_other'] !== '' ? $data['residence_other'] : null,
            ':interest_codes'   => json_encode($data['interest_codes'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ':ip_address'       => $data['ip_address'],
            ':user_agent'       => $data['user_agent'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    private function buildNotificationHtml(array $data): string
    {
        $genderLabel    = $this->options['gender'][$data['gender']] ?? '-';
        $residenceLabel = $this->options['residence'][$data['residence']] ?? '-';

        if ($data['residence'] === 8 && $data['residence_other'] !== '') {
            $residenceLabel .= ' (' . htmlspecialchars($data['residence_other'], ENT_QUOTES, 'UTF-8') . ')';
        }

        $interestLabels = array_map(function (int $code): string {
            return htmlspecialchars($this->options['interests'][$code] ?? (string) $code, ENT_QUOTES, 'UTF-8');
        }, $data['interest_codes']);

        $interestHtml = implode(', ', $interestLabels);
        $name         = htmlspecialchars($data['name'],  ENT_QUOTES, 'UTF-8');
        $phone        = htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8');
        $time         = date('Y-m-d H:i:s');

        $escapedTitle = htmlspecialchars($this->formTitle, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="ko">
<head><meta charset="UTF-8"><title>{$escapedTitle}</title></head>
<body style="font-family:sans-serif;font-size:14px;color:#333;line-height:1.6">
  <h2 style="color:#1a1a1a">{$escapedTitle}</h2>
  <table style="border-collapse:collapse;width:100%;max-width:480px">
    <tr><th style="text-align:left;padding:6px 12px;background:#f5f5f5;border:1px solid #ddd">이름</th><td style="padding:6px 12px;border:1px solid #ddd">{$name}</td></tr>
    <tr><th style="text-align:left;padding:6px 12px;background:#f5f5f5;border:1px solid #ddd">연락처</th><td style="padding:6px 12px;border:1px solid #ddd">{$phone}</td></tr>
    <tr><th style="text-align:left;padding:6px 12px;background:#f5f5f5;border:1px solid #ddd">성별</th><td style="padding:6px 12px;border:1px solid #ddd">{$genderLabel}</td></tr>
    <tr><th style="text-align:left;padding:6px 12px;background:#f5f5f5;border:1px solid #ddd">거주지</th><td style="padding:6px 12px;border:1px solid #ddd">{$residenceLabel}</td></tr>
    <tr><th style="text-align:left;padding:6px 12px;background:#f5f5f5;border:1px solid #ddd">관심분야</th><td style="padding:6px 12px;border:1px solid #ddd">{$interestHtml}</td></tr>
    <tr><th style="text-align:left;padding:6px 12px;background:#f5f5f5;border:1px solid #ddd">접수 시간</th><td style="padding:6px 12px;border:1px solid #ddd">{$time}</td></tr>
  </table>
</body>
</html>
HTML;
    }
}
