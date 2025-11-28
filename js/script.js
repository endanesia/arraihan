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
        const href = link.getAttribute('href');
        
        // If link contains full URL (http:// or https://), allow normal navigation
        if (href && (href.startsWith('http://') || href.startsWith('https://'))) {
            // Let browser handle the navigation normally
            return;
        }
        
        // If link contains a page (like index.php#section), allow normal navigation
        if (href && href.includes('.php#')) {
            // Let browser handle the navigation normally
            return;
        }
        
        // For hash-only links, do smooth scroll
        if (href && href.startsWith('#')) {
            e.preventDefault();
            
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link
            link.classList.add('active');
            
            // Get target section
            const targetSection = document.querySelector(href);
            
            if (targetSection) {
                const offsetTop = targetSection.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
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
        
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const message = document.getElementById('message').value.trim();
        
        // Get Turnstile token
        const turnstileToken = document.querySelector('[name="cf-turnstile-response"]');
        
        // Validate Turnstile
        if (!turnstileToken || !turnstileToken.value) {
            alert('Mohon verifikasi bahwa Anda bukan robot.');
            return;
        }
        
        // Validate fields
        if (!name || !email || !phone) {
            alert('Mohon lengkapi semua field yang wajib diisi.');
            return;
        }
        
        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Format email tidak valid.');
            return;
        }
        
        // Create WhatsApp message
        const whatsappMessage = `
Assalamualaikum, saya ingin berkonsultasi tentang paket umroh.

*Nama:* ${name}
*Email:* ${email}
*No. WhatsApp:* ${phone}
${message ? `*Pesan:* ${message}` : ''}
        `.trim();
        
        // Encode message for URL
        const encodedMessage = encodeURIComponent(whatsappMessage);
        
        // WhatsApp number from settings
        const whatsappNumber = '6282132087805'; // Update with actual number
        
        // Reset form
        contactForm.reset();
        
        // Reset Turnstile
        if (window.turnstile) {
            window.turnstile.reset();
        }
        
        // Open WhatsApp
        window.open(`https://wa.me/${whatsappNumber}?text=${encodedMessage}`, '_blank');
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

// ===== PACKAGE SLIDER MOVED TO SWIPER.JS =====
// Swiper initialization now in index.php inline script

// ===== MUTAWWIF SLIDER INITIALIZATION =====
if (document.querySelector('.mutawwifSwiper')) {
    new Swiper('.mutawwifSwiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.mutawwif .swiper-button-next',
            prevEl: '.mutawwif .swiper-button-prev',
        },
        pagination: {
            el: '.mutawwif .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 30,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 30,
            },
        },
    });
}

// ===== PARTNERS SLIDER INITIALIZATION =====
if (document.querySelector('.partnersSwiper')) {
    new Swiper('.partnersSwiper', {
        slidesPerView: 2,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.partners .swiper-button-next',
            prevEl: '.partners .swiper-button-prev',
        },
        pagination: {
            el: '.partners .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            480: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 4,
                spaceBetween: 25,
            },
            1024: {
                slidesPerView: 6,
                spaceBetween: 30,
            },
        },
    });
}

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

// ===== PARTNER CERTIFICATE MODAL =====
function closeCertificateModal() {
    const modal = document.getElementById('partner-certificate-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Partner certificate click handler
document.addEventListener('DOMContentLoaded', function() {
    const partnerItems = document.querySelectorAll('.partner-clickable');
    
    partnerItems.forEach(item => {
        item.addEventListener('click', function() {
            const certificateUrl = this.getAttribute('data-certificate');
            const partnerName = this.getAttribute('data-partner-name');
            
            if (certificateUrl) {
                const modal = document.getElementById('partner-certificate-modal');
                const image = document.getElementById('certificate-image');
                const nameEl = document.getElementById('certificate-partner-name');
                
                if (modal && image && nameEl) {
                    image.src = certificateUrl;
                    nameEl.textContent = partnerName;
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            }
        });
    });
    
    // Close on overlay click
    const modal = document.getElementById('partner-certificate-modal');
    if (modal) {
        const overlay = modal.querySelector('.certificate-modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', closeCertificateModal);
        }
        
        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                closeCertificateModal();
            }
        });
    }
});

// ===== POPUP BANNER FUNCTIONALITY =====
function closePopup() {
    const popup = document.getElementById('popup-banner-modal');
    if (popup) {
        popup.style.display = 'none';
        document.body.style.overflow = 'auto';
        // Set cookie to not show popup again for 24 hours
        const now = new Date();
        now.setTime(now.getTime() + (24 * 60 * 60 * 1000)); // 24 hours
        document.cookie = "popup_shown=true; expires=" + now.toUTCString() + "; path=/";
    }
}

// Show popup on page load if not shown in last 24 hours
document.addEventListener('DOMContentLoaded', function() {
    // Check if popup exists
    const popup = document.getElementById('popup-banner-modal');
    if (!popup) return;
    
    // Check cookie
    const popupShown = document.cookie.split('; ').find(row => row.startsWith('popup_shown='));
    
    if (!popupShown) {
        // Show popup after 2 second delay to ensure page is fully loaded
        setTimeout(function() {
            popup.style.display = 'flex';
            // Prevent body scroll when popup is shown
            document.body.style.overflow = 'hidden';
        }, 2000);
    }
    
    // Close on overlay click
    const overlay = popup.querySelector('.popup-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            closePopup();
            document.body.style.overflow = 'auto';
        });
    }
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && popup.style.display === 'flex') {
            closePopup();
            document.body.style.overflow = 'auto';
        }
    });
    
    // Update close button to restore scroll
    const closeBtn = popup.querySelector('.popup-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            document.body.style.overflow = 'auto';
        });
    }
});
