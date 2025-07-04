<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$category = isset($_GET['category']) ? clean_input($_GET['category']) : null;
$page_title = $category ? $category . ' Portfolio' : 'Portfolio';
$meta_description = $category ? 
    'Browse our ' . $category . ' photography portfolio' : 
    'Explore our complete photography portfolio including weddings, events, and portraits';

$images = get_portfolio_images($category);
$categories = get_portfolio_categories();

include 'includes/header.php';
?>

<section class="portfolio-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up"><?php echo $category ? htmlspecialchars($category) : 'Portfolio'; ?></h1>
        
        <?php if ($category): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="portfolio.php">Portfolio</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($category); ?></li>
            </ol>
        </nav>
        <?php endif; ?>
        
        <div class="portfolio-filter" data-aos="fade-up">
            <a href="portfolio.php" class="<?php echo !$category ? 'active' : ''; ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="portfolio.php?category=<?php echo urlencode($cat); ?>" 
               class="<?php echo $category == $cat ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="portfolio-gallery">
    <div class="container">
        <?php if (empty($images)): ?>
        <div class="no-results" data-aos="fade-up">
            <p>No images found in this category.</p>
        </div>
        <?php else: ?>
        <div class="gallery" id="lightgallery">
            <?php foreach ($images as $image): ?>
            <a href="assets/uploads/<?php echo $image['image_path']; ?>" 
               class="gallery-item" 
               data-sub-html="<h4><?php echo htmlspecialchars($image['title']); ?></h4><p><?php echo htmlspecialchars($image['description']); ?></p>"
               data-aos="fade-up">
                <img src="assets/uploads/thumbnails/<?php echo $image['image_path']; ?>" 
                     alt="<?php echo htmlspecialchars($image['alt_text']); ?>" 
                     loading="lazy">
                <div class="gallery-overlay">
                    <h3><?php echo htmlspecialchars($image['title']); ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
    // Initialize lightGallery
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('lightgallery')) {
            lightGallery(document.getElementById('lightgallery'), {
                selector: '.gallery-item',
                download: false,
                share: false
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
