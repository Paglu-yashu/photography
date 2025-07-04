<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'About Me | Creative Lens Photography';
$meta_description = 'Learn about the photographer behind Creative Lens and their journey in capturing beautiful moments.';

include 'includes/header.php';
?>

<section class="about-section">
    <div class="container">
        <div class="about-content" data-aos="fade-up">
            <div class="about-text">
                <h1 class="page-title">About Me</h1>
                <p class="lead">Capturing moments that tell your story</p>
                
                <div class="about-story">
                    <h2>My Journey</h2>
                    <p>Photography has been my passion since childhood. What started as a hobby quickly turned into my life's work. I specialize in wedding, portrait, and event photography, with over 10 years of professional experience.</p>
                    <p>My approach combines technical expertise with an artistic eye to create images that are not just photos, but lasting memories.</p>
                </div>
                
                <div class="about-skills">
                    <h2>My Skills</h2>
                    <div class="skills-grid">
                        <div class="skill-item">
                            <h3>Wedding Photography</h3>
                            <div class="skill-bar">
                                <div class="skill-progress" style="width: 95%"></div>
                            </div>
                        </div>
                        <div class="skill-item">
                            <h3>Portrait Photography</h3>
                            <div class="skill-bar">
                                <div class="skill-progress" style="width: 90%"></div>
                            </div>
                        </div>
                        <div class="skill-item">
                            <h3>Photo Editing</h3>
                            <div class="skill-bar">
                                <div class="skill-progress" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="about-image" data-aos="fade-left">
                <img src="assets/images/photographer.jpg" 
                     alt="Professional photographer setting up equipment in a studio" 
                     loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="equipment-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">My Equipment</h2>
        <div class="equipment-grid">
            <div class="equipment-card" data-aos="fade-up">
                <img src="assets/images/camera.jpg" alt="Professional DSLR camera" loading="lazy">
                <h3>Canon EOS R5</h3>
                <p>High-resolution mirrorless camera for stunning detail</p>
            </div>
            <!-- More equipment items -->
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
