<?php
$collaborationFeatures = [
    [
        'image' => 'business/consortium/collaboration/messenger-hd.png',
        'title' => '실시간 메신저',
        'description' => "컨소시엄 구성 후 참여 기업 구성원 간\n실시간 채팅 기능을 제공합니다.",
    ],
    [
        'image' => 'business/consortium/collaboration/ai-meeting-hd.png',
        'title' => '화상회의 · AI 회의록',
        'description' => "버튼 클릭 한 번으로 화상회의 생성과 입장이 가능하며,\nAI가 자동으로 회의록을 작성합니다.",
    ],
    [
        'image' => 'business/consortium/collaboration/documents-hd.png',
        'title' => '공유폴더 및 문서관리',
        'description' => '제안서, 제출서류, 산출물 등을 클라우드 기반으로 기업 간 공유하고 관리합니다.',
    ],
    [
        'image' => 'business/consortium/collaboration/project-tracking-hd.png',
        'title' => '프로젝트 추적 관리',
        'description' => "역할별 할당 과제 및 파트와 마감일을 추적할 수 있으며,\nTo-do 보드 등의 기능을 제공합니다.",
    ],
];
?>
<section class="partnerit-consortium-collaboration" id="consortium-collaboration" aria-labelledby="consortium-collaboration-title">
    <div class="partnerit-consortium-collaboration__glow partnerit-consortium-collaboration__glow--top" aria-hidden="true"></div>
    <div class="partnerit-consortium-collaboration__glow partnerit-consortium-collaboration__glow--bottom" aria-hidden="true"></div>

    <div class="partnerit-consortium-collaboration__inner no-container-xl">
        <header class="partnerit-consortium-collaboration__header">
            <h2 class="partnerit-consortium-collaboration__title f-heading-2 --bold" id="consortium-collaboration-title">
                <span>컨소시엄 구성 후 즉시 하나의</span>
                <span>협업툴에서 통합 관리 가능</span>
            </h2>
            <p class="partnerit-consortium-collaboration__lead f-body-2">
                <span>실시간 커뮤니케이션부터 문서 관리, 프로젝트 트래킹까지 하나의 협업툴에서 통합 제공하며,</span>
                <span>AI가 음성을 인식하고 회의록까지 자동으로 생성</span>
            </p>
        </header>

        <ul class="partnerit-consortium-collaboration__grid">
            <?php foreach ($collaborationFeatures as $index => $feature): ?>
                <li class="partnerit-consortium-collaboration__card">
                    <figure class="partnerit-consortium-collaboration__visual">
                        <?= image_picture($feature['image'], [
                            'alt' => $feature['title'] . ' 화면',
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'class' => 'partnerit-consortium-collaboration__visual-image',
                        ]) ?>
                    </figure>
                    <div class="partnerit-consortium-collaboration__copy">
                        <h3 class="partnerit-consortium-collaboration__card-title f-heading-5 --bold"><?= e($feature['title']) ?></h3>
                        <p class="partnerit-consortium-collaboration__description f-body-3 --medium"><?= e_nl2br_html($feature['description']) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
