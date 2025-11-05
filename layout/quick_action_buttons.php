<?php
/**
 * Quick Action Buttons Component
 * 
 * Reusable action buttons for table rows (View, Edit, Delete)
 * 
 * @param int $id Record ID
 * @param string $module Module name (users, customers, coils, sales, etc.)
 * @param bool $canEdit Whether user can edit
 * @param bool $canDelete Whether user can delete
 * @param bool $canView Whether user can view details
 */

$id = $id ?? 0;
$module = $module ?? '';
$canView = $canView ?? true;
$canEdit = $canEdit ?? true;
$canDelete = $canDelete ?? true;
?>

<div class="btn-group btn-group-sm" role="group">
    <?php if ($canView): ?>
    <a href="/new-stock-system/index.php?page=<?php echo $module; ?>_view&id=<?php echo $id; ?>" 
       class="btn btn-info btn-sm" 
       title="View Details">
        <i class="bi bi-eye"></i>
    </a>
    <?php endif; ?>
    
    <?php if ($canEdit): ?>
    <a href="/new-stock-system/index.php?page=<?php echo $module; ?>_edit&id=<?php echo $id; ?>" 
       class="btn btn-warning btn-sm" 
       title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <?php endif; ?>
    
    <?php if ($canDelete): ?>
    <form method="POST" 
          action="/new-stock-system/controllers/<?php echo $module; ?>/delete/index.php" 
          style="display: inline;" 
          onsubmit="return confirmDelete();">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
            <i class="bi bi-trash"></i>
        </button>
    </form>
    <?php endif; ?>
</div>
