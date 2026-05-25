<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->escape($this->rule['name']) ?> - <?php echo _("Mail Filtering Rules") ?></title>
    <?php foreach ($this->cssUrls as $url): ?>
    <link rel="stylesheet" href="<?php echo $this->escape($url) ?>">
    <?php endforeach ?>
</head>
<body class="horde-responsive">
    <?php echo $this->topbarHtml ?>

    <div class="container">
        <div class="page-header">
            <a href="<?php echo $this->escape($this->backUrl) ?>" class="back-link">
                <span class="back-arrow">‹</span>
                <?php echo _("Back to Rules") ?>
            </a>
            <h1><?php echo $this->escape($this->rule['name']) ?></h1>
            <?php if ($this->rule['disabled']): ?>
            <span class="rule-status rule-status-disabled"><?php echo _("Disabled") ?></span>
            <?php else: ?>
            <span class="rule-status rule-status-active"><?php echo _("Active") ?></span>
            <?php endif ?>
        </div>

        <div class="rule-detail">
            <div class="rule-section">
                <h2><?php echo _("Description") ?></h2>
                <?php if (!empty($this->rule['description'])): ?>
                    <div class="rule-description">
                        <?php echo nl2br($this->escape($this->rule['description'])) ?>
                    </div>
                <?php else: ?>
                    <div class="rule-description empty-text">
                        <?php echo _("No description available") ?>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <div class="rule-actions">
            <a href="<?php echo $this->escape($this->backUrl) ?>" class="btn btn-secondary">
                <?php echo _("Back to Rules") ?>
            </a>
        </div>
    </div>

    <?php foreach ($this->jsUrls as $url): ?>
    <script src="<?php echo $this->escape($url) ?>"></script>
    <?php endforeach ?>
</body>
</html>
