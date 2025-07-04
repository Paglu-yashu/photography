<?php
function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function get_portfolio_categories() {
    global $conn;
    $categories = [];
    
    try {
        $query = "SELECT DISTINCT category FROM portfolio_images";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row['category'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        // Return default categories if table doesn't exist
        if ($e->getCode() == 1146) {
            return ['Wedding', 'Portrait', 'Event', 'Nature'];
        }
        throw $e; // Re-throw other database exceptions
    }
    
    return $categories;
}

function get_portfolio_images($category = null) {
    global $conn;
    $images = [];
    
    try {
        $query = "SELECT * FROM portfolio_images";
        if ($category) {
            $query .= " WHERE category = '" . $conn->real_escape_string($category) . "'";
        }
        $query .= " ORDER BY upload_date DESC";
        $result = $conn->query($query);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $images[] = $row;
            }
        }
    } catch (mysqli_sql_exception $e) {
        // Return empty array if table doesn't exist
        if ($e->getCode() == 1146) {
            return [];
        }
        throw $e; // Re-throw other database exceptions
    }
    
    return $images;
}

function create_thumbnail($source_path, $destination_path, $width, $height) {
    // Check if GD library is installed
    if (!function_exists('gd_info')) {
        return false;
    }

    // Get the image details
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }

    $original_width = $image_info[0];
    $original_height = $image_info[1];
    $mime_type = $image_info['mime'];

    // Calculate aspect ratio
    $ratio = min($width/$original_width, $height/$original_height);
    $new_width = round($original_width * $ratio);
    $new_height = round($original_height * $ratio);

    // Create image resource based on MIME type
    switch ($mime_type) {
        case 'image/jpeg':
            $original_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $original_image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $original_image = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }

    // Create new true color image
    $thumbnail = imagecreatetruecolor($new_width, $new_height);

    // Handle transparency for PNG and GIF
    if ($mime_type == 'image/png' || $mime_type == 'image/gif') {
        imagecolortransparent($thumbnail, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
    }

    // Resize the image
    imagecopyresampled($thumbnail, $original_image, 
                       0, 0, 0, 0, 
                       $new_width, $new_height, 
                       $original_width, $original_height);

    // Save the thumbnail
    switch ($mime_type) {
        case 'image/jpeg':
            imagejpeg($thumbnail, $destination_path, 90);
            break;
        case 'image/png':
            imagepng($thumbnail, $destination_path, 9);
            break;
        case 'image/gif':
            imagegif($thumbnail, $destination_path);
            break;
    }

    // Free up memory
    imagedestroy($original_image);
    imagedestroy($thumbnail);

    return true;
}
?>
