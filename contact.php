<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Contact | Creative Lens Photography';
$meta_description = 'Get in touch to book a session or ask about our photography services.';

// Initialize variables
$name = $email = $message = '';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $subject = clean_input($_POST['subject'] ?? 'General Inquiry');
    $message = clean_input($_POST['message'] ?? '');
    
    // Validate inputs
    if (empty($name)) {
        $errors['name'] = 'Please enter your name';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Please enter your email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Please enter your message';
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        // Prepare email headers
        $to = 'contact@creativelens.com';
        $headers = "From: $name <$email>\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Prepare email content
        $email_content = "<h2>New Contact Form Submission</h2>";
        $email_content .= "<p><strong>Name:</strong> $name</p>";
        $email_content .= "<p><strong>Email:</strong> $email</p>";
        $email_content .= "<p><strong>Subject:</strong> $subject</p>";
        $email_content .= "<p><strong>Message:</strong></p>";
        $email_content .= "<p>$message</p>";
        
        // Send email
        if (mail($to, "New Contact Form: $subject", $email_content, $headers)) {
            $success = true;
            $name = $email = $message = ''; // Clear form on success
            
            // Store in database (optional)
            $query = "INSERT INTO contact_submissions (name, email, subject, message, submitted_at) 
                      VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            $stmt->execute();
        }
    }
}

include 'includes/header.php';
?>

<section class="contact-section">
    <div class="container">
        <div class="contact-content">
            <div class="contact-info" data-aos="fade-up">
                <h1 class="page-title">Get In Touch</h1>
                <p class="lead">Ready to book a session or have questions? Contact me today!</p>
                
                <div class="contact-details">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <p><a href="mailto:contact@creativelens.com">contact@creativelens.com</a></p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <p><a href="tel:+1234567890">(123) 456-7890</a></p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>123 Photography St, Creative City, CA 90210</p>
                    </div>
                </div>
                
                <div class="business-hours">
                    <h3>Business Hours</h3>
                    <p>Monday - Friday: 9 AM - 5 PM</p>
                    <p>Weekends: By appointment only</p>
                </div>
            </div>
            
            <div class="contact-form-container" data-aos="fade-up">
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <p>Thank you for your message! I'll get back to you soon.</p>
                </div>
                <?php endif; ?>
                
                <form id="contact-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        <?php if (isset($errors['name'])): ?>
                        <span class="error"><?php echo $errors['name']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject">
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Wedding Photography">Wedding Photography</option>
                            <option value="Portrait Session">Portrait Session</option>
                            <option value="Event Coverage">Event Coverage</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($message); ?></textarea>
                        <?php if (isset($errors['message'])): ?>
                        <span class="error"><?php echo $errors['message']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="contact-map" data-aos="fade-up">
    <iframe src="https://www.google.com/maps/embed?pb=!..." allowfullscreen="" loading="lazy"></iframe>
</div>

<?php include 'includes/footer.php'; ?>
