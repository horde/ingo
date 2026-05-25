<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo _("Mail Filtering Rules") ?></title>
    <?php foreach ($this->cssUrls as $url): ?>
    <link rel="stylesheet" href="<?php echo $this->escape($url) ?>">
    <?php endforeach ?>
</head>
<body class="horde-responsive">
    <?php echo $this->topbarHtml ?>

    <div class="container">
        <div class="page-header">
            <h1><?php echo _("Mail Filtering Rules") ?></h1>
        </div>

        <?php if (!empty($this->rules)): ?>
            <div class="search-widget">
                <label for="rule-filter" class="sr-only"><?php echo _("Filter rules") ?></label>
                <div class="search-input-wrapper">
                    <input
                        type="search"
                        id="rule-filter"
                        class="form-control"
                        placeholder="<?php echo _("Search rules...") ?>"
                        aria-label="<?php echo _("Filter rules by name") ?>"
                    >
                    <button type="button" class="btn-clear" aria-label="<?php echo _("Clear search") ?>" hidden>
                        ×
                    </button>
                </div>
            </div>

            <ul class="rule-list">
                <?php foreach ($this->rules as $rule): ?>
                    <li class="rule-item" data-rule-name="<?php echo $this->escape($rule['name']) ?>">
                        <a href="<?php echo Horde::url('responsive/rule/' . urlencode($rule['uid'])) ?>" class="rule-link">
                            <?php if (!empty($rule['icon'])): ?>
                                <span class="rule-icon"><?php echo $rule['icon'] ?></span>
                            <?php else: ?>
                                <span class="rule-icon rule-icon-default">📋</span>
                            <?php endif ?>
                            <span class="rule-name"><?php echo $this->escape($rule['name']) ?></span>
                            <span class="rule-chevron">›</span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>

            <div class="empty-state-filtered" hidden>
                <p><?php echo _("No rules match your search.") ?></p>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h2><?php echo _("No Filtering Rules") ?></h2>
                <p><?php echo _("You don't have any mail filtering rules configured yet.") ?></p>
            </div>
        <?php endif ?>
    </div>

    <?php foreach ($this->jsUrls as $url): ?>
    <script src="<?php echo $this->escape($url) ?>"></script>
    <?php endforeach ?>
</body>
</html>
