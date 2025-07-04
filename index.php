<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Professional Photography Services';
$meta_description = 'Creative Lens Photography offers professional photography services for weddings, events, portraits, and more. Browse our portfolio and book your session today.';

$featured_images = get_portfolio_images();
$categories = get_portfolio_categories();

include 'includes/header.php';
?>

<section class="hero-section" data-aos="fade">
    <div class="hero-content">
        <h2>Capturing Life's Precious Moments</h2>
        <p>Professional photography services for weddings, events, and portraits</p>
        <div class="hero-buttons">
            <a href="portfolio.php" class="btn-primary">View Portfolio</a>
            <a href="contact.php" class="btn-secondary">Book a Session</a>
        </div>
    </div>
</section>

<section class="featured-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Featured Work</h2>
        <div class="featured-grid">
            <?php foreach (array_slice($featured_images, 0, 6) as $image): ?>
            <div class="featured-item" data-aos="fade-up" data-aos-delay="<?php echo rand(50, 200); ?>">
                <a href="portfolio.php?category=<?php echo urlencode($image['category']); ?>">
                    <img src="assets/uploads/<?php echo $image['image_path']; ?>" 
                         alt="<?php echo htmlspecialchars($image['alt_text']); ?>" 
                         loading="lazy">
                    <div class="featured-overlay">
                        <h3><?php echo htmlspecialchars($image['title']); ?></h3>
                        <p><?php echo htmlspecialchars($image['category']); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Photo Categories</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
            <div class="category-card" data-aos="fade-up">
                <a href="portfolio.php?category=<?php echo urlencode($category); ?>">
                    <div class="category-image">
                        <?php 
                        $category_images = get_portfolio_images($category);
                        if (!empty($category_images)): 
                        ?>
                        <img src="assets/uploads/<?php echo $category_images[0]['image_path']; ?>" 
                             alt="<?php echo htmlspecialchars($category); ?> photography" 
                             loading="lazy">
                        <?php endif; ?>
                    </div>
                    <h3><?php echo htmlspecialchars($category); ?></h3>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="testimonials-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Client Testimonials</h2>
        <div class="testimonials-slider" data-aos="fade-up">
            <!-- Testimonial items would go here -->
            <div class="testimonial">
                <p>"Absolutely stunning photos! Captured our wedding perfectly."</p>
                <cite>- Sarah & James</cite>
            </div>
            <!-- More testimonials -->
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
