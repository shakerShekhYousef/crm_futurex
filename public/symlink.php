<?php

$targetFolder = __DIR__ . '/../storage';  // Path to storage/uploads
$linkFolder = __DIR__ . '/storage'; // Path to public/storage
print $targetFolder;
// Remove existing link or folder if it exists
if (is_link($linkFolder) || is_dir($linkFolder)) {
    unlink($linkFolder);
    echo "Existing symlink or folder deleted. ";
}

// Create a new symlink
if (symlink($targetFolder, $linkFolder)) {
    echo "Symlink process successfully completed.";
} else {
    echo "Failed to create symlink.";
}

?>
