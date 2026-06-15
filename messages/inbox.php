<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../classes/Message.php';
$msg = new Message();
$inbox = $msg->getInbox($_SESSION['user_id']);
$sent = $msg->getSent($_SESSION['user_id']);
$view = isset($_GET['view']) && $_GET['view'] === 'sent' ? 'sent' : 'inbox';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Messages</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
 <link rel="stylesheet" href="/brilliance/assets/css/messages.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="message-page">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <div>
            <h2 style="margin:0"><?= $view === 'sent' ? 'Sent Messages' : 'Inbox' ?></h2>
            <div style="color:var(--text);font-size:14px"><?= $view === 'sent' ? 'Messages you have sent' : 'Recent messages from tutors and parents' ?></div>
        </div>
        <div style="display:flex;gap:8px;align-items:center">
            <?php if ($view === 'sent'): ?>
                <a class="btn-outline" href="inbox.php">View Inbox</a>
            <?php else: ?>
                <a class="btn-outline" href="inbox.php?view=sent">View Sent</a>
            <?php endif; ?>
            <a class="btn-primary" href="compose.php">Compose</a>
        </div>
    </div>

    <?php if(!empty($_SESSION['flash_success'])): ?><div style="background:#e6ffed;padding:10px;border-radius:6px;color:#155724;margin-bottom:12px"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div><?php endif; ?>

    <?php if ($view === 'sent'): ?>
        <?php if (empty($sent)): ?>
            <p>No sent messages yet.</p>
        <?php else: ?>
            <div class="message-list">
                <?php foreach($sent as $m): ?>
                    <div class="message-card" style="display:flex;gap:12px;align-items:flex-start;">
                        <div style="flex:0 0 62px;">
                            <?php if (!empty($m['receiver_pic'])): ?>
                                <img src="/brilliance/uploads/<?= htmlspecialchars($m['receiver_pic']) ?>" alt="avatar" class="message-avatar" style="width:56px;height:56px;border-radius:10px;object-fit:cover">
                            <?php else: ?>
                                <img src="/brilliance/assets/images/logo.png" alt="avatar" class="message-avatar" style="width:56px;height:56px;border-radius:10px;object-fit:cover">
                            <?php endif; ?>
                        </div>

                        <div style="flex:1;">
                            <div style="display:flex;justify-content:space-between;align-items:start;gap:12px">
                                <div>
                                    <div style="font-weight:700;font-size:15px;color:var(--dark)"><?= htmlspecialchars($m['receiver_name'] ?: 'User') ?></div>
                                    <div class="message-subject"><?= htmlspecialchars($m['subject'] ?: '(no subject)') ?></div>
                                </div>
                                <div style="text-align:right;color:#8891a6;font-size:13px">
                                    <?= htmlspecialchars($m['created_at']) ?>
                                </div>
                            </div>

                            <div class="message-snippet" style="margin-top:8px;color:var(--text)"><?= htmlspecialchars(mb_strimwidth(strip_tags($m['body']),0,200,'...')) ?></div>
                            <div class="message-actions" style="margin-top:12px;display:flex;gap:8px">
                                <a class="btn-primary" href="view.php?id=<?= $m['id'] ?>">Open</a>
                                <a class="btn-outline" href="compose.php?receiver_id=<?= (int)$m['receiver_id'] ?>">Reply</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php if (empty($inbox)): ?>
            <p>No messages.</p>
        <?php else: ?>
            <div class="message-list">
                <?php foreach($inbox as $m): ?>
                    <div class="message-card" style="display:flex;gap:12px;align-items:flex-start;">
                        <div style="flex:0 0 62px;">
                            <?php if (!empty($m['sender_pic'])): ?>
                                <img src="/brilliance/uploads/<?= htmlspecialchars($m['sender_pic']) ?>" alt="avatar" class="message-avatar" style="width:56px;height:56px;border-radius:10px;object-fit:cover">
                            <?php else: ?>
                                <img src="/brilliance/assets/images/logo.png" alt="avatar" class="message-avatar" style="width:56px;height:56px;border-radius:10px;object-fit:cover">
                            <?php endif; ?>
                        </div>

                        <div style="flex:1;">
                            <div style="display:flex;justify-content:space-between;align-items:start;gap:12px">
                                <div>
                                    <div style="font-weight:700;font-size:15px;color:var(--dark)"><?= htmlspecialchars($m['sender_name'] ?: 'User') ?></div>
                                    <div class="message-subject"><?= htmlspecialchars($m['subject'] ?: '(no subject)') ?></div>
                                </div>
                                <div style="text-align:right;color:#8891a6;font-size:13px">
                                    <?= htmlspecialchars($m['created_at']) ?>
                                    <?php if (isset($m['is_read']) && !$m['is_read']): ?>
                                        <div class="unread-badge" style="margin-top:6px;">New</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="message-snippet" style="margin-top:8px;color:var(--text)"><?= htmlspecialchars(mb_strimwidth(strip_tags($m['body']),0,200,'...')) ?></div>
                            <div class="message-actions" style="margin-top:12px;display:flex;gap:8px">
                                <a class="btn-primary" href="view.php?id=<?= $m['id'] ?>">Open</a>
                                <a class="btn-outline" href="chat.php?user=<?= (int)$m['sender_id'] ?>">Chat</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
