<?php
// footer.php
$current_year = date("Y");
?>
<footer class="footer mt-auto py-4 text-white" style="background: linear-gradient(90deg, #2c3e50 0%, #4a6888 100%); border-top: 4px solid #f39c12;">
    <div class="container">
        <div class="row align-items-center justify-content-center gy-3">

            <div class="col-lg-4 col-md-6 text-center footer-section footer-college animate__animated animate__fadeInUp">
                <div class="card-hover-effect">
                    <h5 class="mb-2 text-warning"><i class="fas fa-university me-2 pulse-icon"></i>Institution</h5>
                    <p class="mb-1 small">Kongu Velalar Polytechnic College</p>
                    <p class="mb-0 small">Perundurai, Erode - 638052</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 text-center footer-section footer-team animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-hover-effect">
                    <h5 class="mb-2 text-warning"><i class="fas fa-users me-2 pulse-icon"></i>Development Team</h5>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-1 team-leader"><strong><i class="fas fa-crown text-warning me-1 shine-effect"></i> Abdul Ajeez N (Team Head)</strong></li>
                        <li class="team-member-animate"><i class="fas fa-user-cog me-1 opacity-75"></i> Danielraj A</li>
                        <li class="team-member-animate"><i class="fas fa-user-cog me-1 opacity-75"></i> Abinesh K</li>
                        <li class="team-member-animate"><i class="fas fa-user-cog me-1 opacity-75"></i> Abishek S</li>
                        <li class="team-member-animate"><i class="fas fa-user-cog me-1 opacity-75"></i> Alwin Nishanth N</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-12 text-center footer-section footer-copy animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div class="mb-2 coin-container">
                    <i class="fas fa-coins fa-2x text-success floating-icon"></i>
                    <i class="fas fa-rupee-sign fa-2x text-success floating-icon" style="animation-delay: 0.5s;"></i>
                    <i class="fas fa-chart-line fa-2x text-warning floating-icon" style="animation-delay: 1s;"></i>
                </div>
                <small class="copyright-text">Expense Tracker &copy; <?php echo $current_year; ?>. All Rights Reserved.</small>
                <div class="social-icons mt-2">
                    <a href="#" class="social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Floating elements -->
    <div class="floating-elements">
        <div class="floating-element" style="left: 5%; top: 20%; animation-delay: 0s;"></div>
        <div class="floating-element" style="left: 25%; top: 60%; animation-delay: 2s;"></div>
        <div class="floating-element" style="left: 45%; top: 30%; animation-delay: 1s;"></div>
        <div class="floating-element" style="left: 65%; top: 70%; animation-delay: 3s;"></div>
        <div class="floating-element" style="left: 85%; top: 40%; animation-delay: 0.5s;"></div>
    </div>
    
    <!-- Watermark that's difficult to remove -->
    <div class="watermark" aria-hidden="true">
        <div class="watermark-text" data-text="Powered by JS">Powered by JS</div>
    </div>
</footer>

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Add Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<style>
    /* Base Footer Styles */
    .footer {
        box-shadow: 0 -5px 15px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
        z-index: 10;
    }
    .footer h5 {
        text-transform: uppercase;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    .footer p, .footer small, .footer li {
        color: #e0e0e0;
        line-height: 1.6;
    }
    .footer li i {
        width: 1.2em;
        text-align: center;
    }
    
    /* Card Hover Effect (Tailwind-inspired) */
    .card-hover-effect {
        transition: all 0.3s ease;
        padding: 1.5rem;
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .card-hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Team Member Animations */
    .team-leader {
        animation: fadeInSpecial 1s ease forwards;
        position: relative;
    }
    .team-leader::after {
        content: '';
        position: absolute;
        width: 60%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(243, 156, 18, 0.5), transparent);
        bottom: -5px;
        left: 20%;
        transform: scaleX(0);
        animation: lineExpand 1.5s ease-out forwards;
        animation-delay: 1.5s;
    }
    @keyframes lineExpand {
        to { transform: scaleX(1); }
    }
    
    .team-member-animate {
        opacity: 0;
        transform: translateX(-20px);
        animation: slideInTeamMember 0.5s ease-out forwards;
        position: relative;
        padding: 3px 0;
    }
    .team-member-animate:hover {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    .team-member-animate:nth-child(2) { animation-delay: 0.6s; }
    .team-member-animate:nth-child(3) { animation-delay: 0.8s; }
    .team-member-animate:nth-child(4) { animation-delay: 1.0s; }
    .team-member-animate:nth-child(5) { animation-delay: 1.2s; }
    .team-member-animate:nth-child(6) { animation-delay: 1.4s; }
    
    @keyframes slideInTeamMember {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeInSpecial {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Icon Animations */
    .pulse-icon {
        animation: pulseAnimation 2.5s infinite ease-in-out;
        display: inline-block;
    }
    @keyframes pulseAnimation {
        0% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.2); opacity: 1; filter: drop-shadow(0 0 5px rgba(255, 193, 7, 0.7)); }
        100% { transform: scale(1); opacity: 0.8; }
    }
    
    .floating-icon {
        display: inline-block;
        animation: floatAndShine 4s infinite ease-in-out;
        filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.3));
        position: relative;
    }
    @keyframes floatAndShine {
        0% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
        25% { transform: translateY(-8px) rotate(8deg); opacity: 1; filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.7)); }
        50% { transform: translateY(0) rotate(0deg); opacity: 0.8; }
        75% { transform: translateY(-6px) rotate(-8deg); opacity: 1; filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.7)); }
        100% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
    }
    
    /* Coin Container */
    .coin-container {
        position: relative;
        height: 50px;
        perspective: 500px;
    }
    
    /* Shine Effect */
    .shine-effect {
        position: relative;
        animation: shineEffect 3s infinite;
    }
    @keyframes shineEffect {
        0% { text-shadow: 0 0 2px gold; }
        50% { text-shadow: 0 0 12px gold, 0 0 20px gold; }
        100% { text-shadow: 0 0 2px gold; }
    }
    
    /* Copyright Text Animation */
    .copyright-text {
        display: inline-block;
        position: relative;
        overflow: hidden;
    }
    .copyright-text::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        animation: slideRightLeft 3s infinite;
    }
    @keyframes slideRightLeft {
        0% { transform: translateX(-100%); }
        50% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    
    /* Social Icons */
    .social-icons {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 8px;
    }
    .social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 14px;
        transition: all 0.3s ease;
        opacity: 0;
        animation: fadeInRotate 0.5s ease forwards;
    }
    .social-icon:nth-child(1) { animation-delay: 1.6s; }
    .social-icon:nth-child(2) { animation-delay: 1.8s; }
    .social-icon:nth-child(3) { animation-delay: 2.0s; }
    .social-icon:nth-child(4) { animation-delay: 2.2s; }
    
    .social-icon:hover {
        background: #f39c12;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(243, 156, 18, 0.5);
    }
    
    @keyframes fadeInRotate {
        from {
            opacity: 0;
            transform: scale(0) rotate(-90deg);
        }
        to {
            opacity: 1;
            transform: scale(1) rotate(0);
        }
    }
    
    /* Floating Elements - Abstract background shapes */
    .floating-elements {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    }
    .floating-element {
        position: absolute;
        width: 10px;
        height: 10px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        animation: float 15s infinite ease-in-out;
    }
    @keyframes float {
        0% { transform: translate(0, 0) scale(1); opacity: 0.2; }
        25% { transform: translate(10px, 10px) scale(1.5); opacity: 0.3; }
        50% { transform: translate(5px, -5px) scale(1); opacity: 0.2; }
        75% { transform: translate(-10px, 10px) scale(1.2); opacity: 0.3; }
        100% { transform: translate(0, 0) scale(1); opacity: 0.2; }
    }
    
    /* Background gradient and particles */
    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 10% 20%, rgba(255,255,255,0.03) 1px, transparent 2px),
            radial-gradient(circle at 40% 70%, rgba(255,255,255,0.03) 2px, transparent 2px),
            radial-gradient(circle at 70% 30%, rgba(255,255,255,0.03) 1px, transparent 1px),
            radial-gradient(circle at 90% 90%, rgba(255,255,255,0.03) 2px, transparent 2px);
        background-size: 60px 60px;
        pointer-events: none;
        z-index: 1;
        animation: backgroundShift 30s infinite linear;
    }
    
    @keyframes backgroundShift {
        0% { background-position: 0 0; }
        100% { background-position: 60px 60px; }
    }
    
    /* Watermark styles */
    .watermark {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 10;
        overflow: hidden;
    }
    .watermark::before {
        content: "Powered by JS";
        position: absolute;
        bottom: 5px;
        right: 10px;
        font-size: 10px;
        color: rgba(255,255,255,0.3);
        z-index: 999;
        letter-spacing: 1px;
        font-weight: bold;
    }
    .watermark-text {
        position: absolute;
        bottom: 0;
        right: 0;
        font-size: 10px;
        font-weight: bold;
        color: rgba(255,255,255,0.15);
        transform: rotate(-45deg) translate(25%, -50%);
        transform-origin: bottom right;
        text-transform: uppercase;
        letter-spacing: 1px;
        white-space: nowrap;
        z-index: 999;
    }
    .watermark-text::after {
        content: attr(data-text);
        position: absolute;
        bottom: 5px;
        right: 10px;
        font-size: 10px;
        color: rgba(255,255,255,0.2);
        z-index: 999;
    }
    
    /* Ensure content is above background */
    .container {
        position: relative;
        z-index: 2;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-hover-effect {
            padding: 1rem;
        }
        .footer h5 {
            font-size: 0.9rem;
        }
    }
</style>

<!-- Script to make watermark more difficult to remove and handle animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS (Animate on Scroll) effect for team members
    function animateOnViewport() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-viewport');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        document.querySelectorAll('.footer-section').forEach(el => {
            observer.observe(el);
        });
    }
    
    // Create watermark text elements dynamically
    function addDynamicWatermark() {
        const newWatermark = document.createElement('div');
        newWatermark.className = 'watermark-text';
        newWatermark.setAttribute('data-text', 'Powered by JS');
        newWatermark.textContent = 'Powered by JS';
        newWatermark.style.opacity = '0.2';
        newWatermark.style.position = 'absolute';
        newWatermark.style.bottom = Math.random() * 30 + 'px';
        newWatermark.style.right = Math.random() * 50 + 'px';
        document.querySelector('.watermark').appendChild(newWatermark);
    }
    
    // Add interactive hover effects for team members
    document.querySelectorAll('.team-member-animate').forEach(member => {
        member.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        member.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Recreate watermark if someone tries to remove it
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.removedNodes.length > 0) {
                for (let node of mutation.removedNodes) {
                    if (node.classList && 
                       (node.classList.contains('watermark') || 
                        node.classList.contains('watermark-text'))) {
                        addDynamicWatermark();
                    }
                }
            }
        });
    });
    
    observer.observe(document.querySelector('footer'), { 
        childList: true,
        subtree: true
    });
    
    // Periodically check if watermark exists
    setInterval(function() {
        if (document.querySelectorAll('.watermark-text').length < 2) {
            addDynamicWatermark();
        }
    }, 5000);
    
    // Run animations
    animateOnViewport();
});
</script>