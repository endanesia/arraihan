// ===== NAVIGATION SCROLL EFFECT =====
const header = document.getElementById('header');
const navLinks = document.querySelectorAll('.nav-link');

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// ===== MOBILE MENU TOGGLE =====
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');

navToggle.addEventListener('click', () => {
    navMenu.classList.toggle('active');
});

// Close menu when clicking nav links
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
    });
});

// ===== SMOOTH SCROLL FOR NAVIGATION =====
navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Remove active class from all links
        navLinks.forEach(l => l.classList.remove('active'));
        
        // Add active class to clicked link
        link.classList.add('active');
        
        // Get target section
        const targetId = link.getAttribute('href');
        const targetSection = document.querySelector(targetId);
        
        if (targetSection) {
            const offsetTop = targetSection.offsetTop - 80;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ===== ACTIVE NAVIGATION ON SCROLL =====
const sections = document.querySelectorAll('section[id]');

window.addEventListener('scroll', () => {
    const scrollY = window.pageYOffset;
    
    sections.forEach(section => {
        const sectionHeight = section.offsetHeight;
        const sectionTop = section.offsetTop - 100;
        const sectionId = section.getAttribute('id');
        const correspondingLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
        
        if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
            navLinks.forEach(link => link.classList.remove('active'));
            if (correspondingLink) {
                correspondingLink.classList.add('active');
            }
        }
    });
});

// ===== SCROLL TO TOP BUTTON =====
const scrollTopBtn = document.getElementById('scrollTop');

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        scrollTopBtn.classList.add('show');
    } else {
        scrollTopBtn.classList.remove('show');
    }
});

scrollTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// ===== CONTACT FORM SUBMISSION =====
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const paket = document.getElementById('paket').value;
        const message = document.getElementById('message').value;
        
        // Create WhatsApp message
        const whatsappMessage = `
Assalamualaikum, saya ingin berkonsultasi tentang paket umroh.

*Nama:* ${name}
*Email:* ${email}
*No. WhatsApp:* ${phone}
*Paket:* ${paket}
*Pesan:* ${message}
        `.trim();
        
        // Encode message for URL
        const encodedMessage = encodeURIComponent(whatsappMessage);
        
        // WhatsApp number (ganti dengan nomor yang sesuai)
        const whatsappNumber = '6281234567890';
        
        // Open WhatsApp
        window.open(`https://wa.me/${whatsappNumber}?text=${encodedMessage}`, '_blank');
        
        // Reset form
        contactForm.reset();
        
        // Show success message
        alert('Terima kasih! Anda akan diarahkan ke WhatsApp untuk melanjutkan percakapan.');
    });
}

// ===== INTERSECTION OBSERVER FOR ANIMATIONS =====
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe all cards and content sections
const animatedElements = document.querySelectorAll(`
    .keunggulan-card,
    .paket-card,
    .galeri-item,
    .video-item,
    .info-item,
    .partner-item
`);

animatedElements.forEach(element => {
    element.style.opacity = '0';
    element.style.transform = 'translateY(30px)';
    element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(element);
});

// ===== COUNTER ANIMATION REMOVED =====
// Counter animation has been completely removed for static display

// ===== LAZY LOADING IMAGES =====
const lazyImages = document.querySelectorAll('img[data-src]');

const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            imageObserver.unobserve(img);
        }
    });
});

lazyImages.forEach(img => imageObserver.observe(img));

// ===== GALLERY LIGHTBOX =====
const galeriItems = document.querySelectorAll('.galeri-item');

galeriItems.forEach(item => {
    item.addEventListener('click', () => {
        const img = item.querySelector('img');
        if (img) {
            // Create lightbox
            const lightbox = document.createElement('div');
            lightbox.className = 'lightbox';
            lightbox.innerHTML = `
                <div class="lightbox-content">
                    <span class="lightbox-close">&times;</span>
                    <img src="${img.src}" alt="${img.alt}">
                </div>
            `;
            
            // Add lightbox styles
            lightbox.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.95);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.3s ease;
            `;
            
            const lightboxContent = lightbox.querySelector('.lightbox-content');
            lightboxContent.style.cssText = `
                position: relative;
                max-width: 90%;
                max-height: 90%;
            `;
            
            const lightboxImg = lightbox.querySelector('img');
            lightboxImg.style.cssText = `
                max-width: 100%;
                max-height: 90vh;
                object-fit: contain;
            `;
            
            const closeBtn = lightbox.querySelector('.lightbox-close');
            closeBtn.style.cssText = `
                position: absolute;
                top: -40px;
                right: 0;
                font-size: 40px;
                color: white;
                cursor: pointer;
                font-weight: 300;
            `;
            
            document.body.appendChild(lightbox);
            document.body.style.overflow = 'hidden';
            
            // Close lightbox
            const closeLightbox = () => {
                lightbox.remove();
                document.body.style.overflow = 'auto';
            };
            
            closeBtn.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', (e) => {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            });
        }
    });
});

// ===== VIDEO PLAY SIMULATION =====
const videoItems = document.querySelectorAll('.video-item');

videoItems.forEach(item => {
    item.addEventListener('click', () => {
        alert('Video akan diputar. Dalam implementasi nyata, video akan diputar di modal atau redirect ke YouTube.');
        // Dalam implementasi nyata, Anda bisa membuka video di modal atau redirect ke YouTube
    });
});

// ===== SMOOTH SCROLL FOR ALL INTERNAL LINKS =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        
        // Skip if it's just '#'
        if (href === '#') {
            e.preventDefault();
            return;
        }
        
        const targetElement = document.querySelector(href);
        
        if (targetElement) {
            e.preventDefault();
            const offsetTop = targetElement.offsetTop - 80;
            
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ===== PREVENT FORM DOUBLE SUBMISSION =====
const forms = document.querySelectorAll('form');

forms.forEach(form => {
    let isSubmitting = false;
    
    form.addEventListener('submit', (e) => {
        if (isSubmitting) {
            e.preventDefault();
            return;
        }
        
        isSubmitting = true;
        
        // Re-enable after 3 seconds
        setTimeout(() => {
            isSubmitting = false;
        }, 3000);
    });
});

// ===== ACCESSIBILITY: ESC KEY TO CLOSE MOBILE MENU =====
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        navMenu.classList.remove('active');
    }
});

// ===== PAGE LOAD ANIMATION =====
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';
    
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// ===== PACKAGE SLIDER FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
    const packageSlider = document.querySelector('.package-slider');
    
    if (!packageSlider) return;
    
    const slides = document.querySelectorAll('.package-slide');
    const dotsContainer = document.querySelector('.package-slider-dots');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    
    let currentSlide = 0;
    let isAutoPlaying = true;
    let autoPlayInterval;
    
    // Create dots
    if (dotsContainer) {
        slides.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.className = 'slider-dot';
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });
    }
    
    const dots = document.querySelectorAll('.slider-dot');
    
    // Show specific slide
    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }
    
    // Go to specific slide
    function goToSlide(index) {
        currentSlide = index;
        showSlide(currentSlide);
        resetAutoPlay();
    }
    
    // Next slide
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    // Previous slide
    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    }
    
    // Auto play functionality
    function startAutoPlay() {
        if (isAutoPlaying && slides.length > 1) {
            autoPlayInterval = setInterval(nextSlide, 5000);
        }
    }
    
    function stopAutoPlay() {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
        }
    }
    
    function resetAutoPlay() {
        stopAutoPlay();
        if (isAutoPlaying) {
            startAutoPlay();
        }
    }
    
    // Event listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoPlay();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoPlay();
        });
    }
    
    // Pause auto play on hover
    packageSlider.addEventListener('mouseenter', stopAutoPlay);
    packageSlider.addEventListener('mouseleave', () => {
        if (isAutoPlaying) startAutoPlay();
    });
    
    // Touch/swipe support for mobile
    let startX = 0;
    let startY = 0;
    let isDragging = false;
    
    packageSlider.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = true;
        stopAutoPlay();
    }, { passive: true });
    
    packageSlider.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        
        const currentX = e.touches[0].clientX;
        const currentY = e.touches[0].clientY;
        const diffX = startX - currentX;
        const diffY = startY - currentY;
        
        // Prevent default if horizontal swipe is detected
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 10) {
            e.preventDefault();
        }
    }, { passive: false });
    
    packageSlider.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        
        const endX = e.changedTouches[0].clientX;
        const diffX = startX - endX;
        
        // Minimum swipe distance
        if (Math.abs(diffX) > 50) {
            if (diffX > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        }
        
        isDragging = false;
        resetAutoPlay();
    }, { passive: true });
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!packageSlider.matches(':hover')) return;
        
        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                prevSlide();
                resetAutoPlay();
                break;
            case 'ArrowRight':
                e.preventDefault();
                nextSlide();
                resetAutoPlay();
                break;
        }
    });
    
    // Initialize slider
    if (slides.length > 0) {
        showSlide(0);
        startAutoPlay();
    }
    
    // Pause/resume auto play when page visibility changes
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoPlay();
        } else if (isAutoPlaying) {
            startAutoPlay();
        }
    });
    
    // Intersection Observer to pause slider when not visible
    const sliderObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (isAutoPlaying) startAutoPlay();
            } else {
                stopAutoPlay();
            }
        });
    }, { threshold: 0.5 });
    
    sliderObserver.observe(packageSlider);
});

// ===== PACKAGE DETAIL PAGE FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
    // WhatsApp consultation button
    const consultationBtns = document.querySelectorAll('.btn-consultation');
    
    consultationBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const packageName = this.dataset.package || 'Paket Umroh';
            const packagePrice = this.dataset.price || '';
            
            let message = `Assalamualaikum, saya tertarik dengan ${packageName}`;
            
            if (packagePrice) {
                message += ` dengan harga ${packagePrice}`;
            }
            
            message += '. Mohon informasi lebih lanjut.';
            
            const encodedMessage = encodeURIComponent(message);
            const whatsappNumber = '6281234567890'; // Ganti dengan nomor yang sesuai
            
            window.open(`https://wa.me/${whatsappNumber}?text=${encodedMessage}`, '_blank');
        });
    });
    
    // Booking button
    const bookingBtns = document.querySelectorAll('.btn-booking');
    
    bookingBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const packageName = this.dataset.package || 'Paket Umroh';
            const packagePrice = this.dataset.price || '';
            
            let message = `Assalamualaikum, saya ingin melakukan booking untuk ${packageName}`;
            
            if (packagePrice) {
                message += ` dengan harga ${packagePrice}`;
            }
            
            message += '. Mohon dibantu untuk proses selanjutnya.';
            
            const encodedMessage = encodeURIComponent(message);
            const whatsappNumber = '6281234567890'; // Ganti dengan nomor yang sesuai
            
            window.open(`https://wa.me/${whatsappNumber}?text=${encodedMessage}`, '_blank');
        });
    });
});

// ===== CONSOLE MESSAGE =====
console.log(`
%c🕌 Raihan Travelindo 
%cWebsite Travel Umroh & Haji Terpercaya
%cDeveloped with ❤️
`, 
'color: #1a6b4a; font-size: 24px; font-weight: bold;',
'color: #d4a518; font-size: 16px;',
'color: #6c757d; font-size: 12px;'
);
