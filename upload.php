<?php
require_once __DIR__ . '/includes/auth.php';
   require_once __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$page_title = 'Upload Images';
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
        $upload_dir = '../../assets/uploads/';
        $thumbnail_dir = $upload_dir . 'thumbnails/';
        
        // Create directories if they don't exist
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        if (!is_dir($thumbnail_dir)) mkdir($thumbnail_dir, 0755, true);
        
        // Process each uploaded file
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            // Validate each file
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = 'Error uploading file: ' . $_FILES['images']['name'][$i];
                continue;
            }
            
            // Validate image type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['images']['type'][$i];
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = 'Invalid file type: ' . $_FILES['images']['name'][$i];
                continue;
            }
            
            // Validate file size (e.g., 5MB max)
            if ($_FILES['images']['size'][$i] > 5 * 1024 * 1024) {
                $errors[] = 'File too large: ' . $_FILES['images']['name'][$i];
                continue;
            }
            
            // Generate unique filename
            $file_ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('img_', true) . '.' . $file_ext;
            
            // Set upload paths
            $target_path = $upload_dir . $filename;
            $thumbnail_path = $thumbnail_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_path)) {
                // Create thumbnail
                create_thumbnail($target_path, $thumbnail_path, 300, 200);
                
                // Get form data
                $title = clean_input($_POST['title'][$i] ?? 'Untitled');
                $category = clean_input($_POST['category'][$i] ?? 'General');
                $description = clean_input($_POST['description'][$i] ?? '');
                $alt_text = clean_input($_POST['alt_text'][$i]) ?: $title;
                
                // Insert into database
                $query = "INSERT INTO portfolio_images (title, category, description, image_path, alt_text, upload_date) 
                          VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $title, $category, $description, $filename, $alt_text);
                
                if ($stmt->execute()) {
                    $success = true;
                    
                    // Log activity
                    $activity = "Uploaded image: $title";
                    $conn->query("INSERT INTO admin_activity (admin_username, activity, activity_date) 
                                 VALUES ('{$_SESSION['admin_username']}', '$activity', NOW())");
                } else {
                    $errors[] = 'Database error: ' . $stmt->error;
                }
            } else {
                $errors[] = 'Failed to move uploaded file: ' . $_FILES['images']['name'][$i];
            }
        }
    } else {
        $errors[] = 'No files were uploaded';
    }
}

include '../includes/header-admin.php';
?>

<div class="admin-upload">
    <h1>Upload Images</h1>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <h3>Errors occurred:</h3>
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php elseif ($success): ?>
    <div class="alert alert-success">
        <p>Images uploaded successfully!</p>
    </div>
    <?php endif; ?>
    
    <form id="upload-form" method="POST" enctype="multipart/form-data">
        <div class="upload-area" id="drop-area">
            <div class="upload-instructions">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Drag & drop images here or click to browse</p>
                <input type="file" id="file-input" name="images[]" multiple accept="image/*">
            </div>
        </div>
        
        <div id="file-list" class="file-list-container">
            <!-- Files will be listed here with their metadata -->
        </div>
        
        <div class="submit-section">
            <button type="submit" class="btn-primary" id="upload-button" disabled>
                <i class="fas fa-upload"></i> Upload Images
            </button>
        </div>
    </form>
</div>

<script>
// Drag and drop functionality
const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('file-input');
const fileList = document.getElementById('file-list');
const uploadButton = document.getElementById('upload-button');

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

// Highlight drop area when item is dragged over it
['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

// Handle dropped files
dropArea.addEventListener('drop', handleDrop, false);
fileInput.addEventListener('change', handleFiles, false);

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight() {
    dropArea.classList.add('highlight');
}

function unhighlight() {
    dropArea.classList.remove('highlight');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files;
    handleFiles({target: {files: files}});
}

function handleFiles(e) {
    const files = e.target.files;
    if (files.length === 0) return;
    
    fileList.innerHTML = '';
    uploadButton.disabled = false;
    
    Array.from(files).forEach(file => {
        if (!file.type.match('image.*')) return;
        
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        
        const preview = document.createElement('div');
        preview.className = 'file-preview';
        
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        
        const info = document.createElement('div');
        info.className = 'file-info';
        
        const name = document.createElement('span');
        name.className = 'file-name';
        name.textContent = file.name;
        
        const size = document.createElement('span');
        size.className = 'file-size';
        size.textContent = formatFileSize(file.size);
        
        const fields = document.createElement('div');
        fields.className = 'file-fields';
        
        const nameField = createField('title[]', 'Title', file.name.replace(/\.[^/.]+$/, ""));
        const categoryField = createField('category[]', 'Category', 'General');
        const altField = createField('alt_text[]', 'Alt Text', file.name.replace(/\.[^/.]+$/, ""));
        const descField = createField('description[]', 'Description', '', true);
        
        fields.appendChild(nameField);
        fields.appendChild(categoryField);
        fields.appendChild(altField);
        fields.appendChild(descField);
        
        preview.appendChild(img);
        info.appendChild(name);
        info.appendChild(size);
        
        fileItem.appendChild(preview);
        fileItem.appendChild(info);
        fileItem.appendChild(fields);
        
        fileList.appendChild(fileItem);
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function createField(name, label, value, isTextarea = false) {
    const container = document.createElement('div');
    container.className = 'form-field';
    
    const labelEl = document.createElement('label');
    labelEl.textContent = label;
    
    let inputEl;
    if (isTextarea) {
        inputEl = document.createElement('textarea');
        inputEl.rows = 3;
    } else {
        inputEl = document.createElement('input');
        inputEl.type = 'text';
    }
    inputEl.name = name;
    inputEl.value = value;
    
    // Make categories a dropdown for the first field
    if (label === 'Category') {
        const select = document.createElement('select');
        select.name = name;
        
        const categories = ['Wedding', 'Portrait', 'Event', 'Nature', 'Travel', 'Other'];
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            if (cat === value) option.selected = true;
            select.appendChild(option);
        });
        
        // Replace input with select
        container.removeChild(inputEl);
        container.appendChild(labelEl);
        container.appendChild(select);
        return container;
    }
    
    container.appendChild(labelEl);
    container.appendChild(inputEl);
    
    return container;
}
</script>

<?php include '../includes/footer-admin.php'; ?>
