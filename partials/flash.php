<?php
$flash = getFlash();
if ($flash): ?>
<div class="position-fixed" style="top:20px; right:20px; z-index:9999; min-width:300px;">
    <div class="alert alert-<?php echo $flash['type']==='success'?'success':'info'; ?> alert-dismissible fade show shadow">
        <?php echo htmlspecialchars($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>


