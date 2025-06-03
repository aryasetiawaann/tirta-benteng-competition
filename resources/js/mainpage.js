import "./bootstrap";

document.addEventListener("DOMContentLoaded", function() {
    // ====================================================================
    // NAVBAR AND SIDEBAR FUNCTIONALITY
    // ====================================================================

    const burger = document.querySelector(".nav-burger");
    const close = document.querySelector(".sidebar-close");
    const sidebar = document.querySelector(".sidebar");

    document.addEventListener("click", (event) => {
    if (burger && burger.contains(event.target)) {
        if (sidebar.style.display === "block") {
            sidebar.style.display = "none";
        } else {
            sidebar.style.display = "block";
        }
    } else if (close && close.contains(event.target)) {
        sidebar.style.display = "none";
        } else if (sidebar && !sidebar.contains(event.target)) {
        sidebar.style.display = "none";
    }
});

    // Navbar color change on scroll
document.addEventListener("scroll", () => {
    const navbar = document.querySelector(".navbar");
    const scrollHeight = window.innerHeight - navbar.offsetHeight;

    if (window.scrollY >= scrollHeight) {
        navbar.classList.add("scrolled");
    } else {
        navbar.classList.remove("scrolled");
    }
});

    // ====================================================================
    // JADWAL SECTION FUNCTIONALITY
    // ====================================================================
    
    let currentIndex = 0;
    const kompetisiContainers = document.querySelectorAll('.jadwal-container');
    const totalKompetisi = kompetisiContainers.length;

        const prevBtn = document.getElementById('jadPrevBtn');
        const nextBtn = document.getElementById('jadNextBtn');
        
    if(prevBtn || nextBtn){
        prevBtn.addEventListener('click', function () {
            kompetisiContainers[currentIndex].style.display = 'none';
            currentIndex = (currentIndex - 1 + totalKompetisi) % totalKompetisi;
            kompetisiContainers[currentIndex].style.display = 'block';
        });
    
        nextBtn.addEventListener('click', function () {
            kompetisiContainers[currentIndex].style.display = 'none';
            currentIndex = (currentIndex + 1) % totalKompetisi;
            kompetisiContainers[currentIndex].style.display = 'block';
        });
        }

    // ====================================================================
    // RIWAYAT CAROUSEL SECTION
    // ====================================================================

    let riwayatCurrentIndex = 0;
    const riwayatTrack = document.getElementById('riwayatTrack');
    const riwayatPrevBtn = document.getElementById('riwayatPrevBtn');
    const riwayatNextBtn = document.getElementById('riwayatNextBtn');
    const riwayatCards = document.querySelectorAll('.carousel-card');
    let isTransitioning = false;
    let autoPlayInterval;

    console.log('Mobile-Fixed Carousel - Total cards found:', riwayatCards.length);

    if (riwayatTrack && riwayatCards.length > 0) {
        const totalCards = riwayatCards.length;

        // Responsive card width calculation - sesuai dengan CSS
        function getCardWidth() {
            const screenWidth = window.innerWidth;
            if (screenWidth <= 400) {
                return screenWidth - 120;
            } else if (screenWidth <= 580) {
                return screenWidth - 130;
            } else if (screenWidth <= 850) {
                return 320;
            } else {
                return 350;
            }
        }

        // Calculate gap between cards
        function getCardGap() {
            const screenWidth = window.innerWidth;
            if (screenWidth <= 580) {
                return 25;
            } else {
                return 30;
            }
        }

        // Update carousel function
        function updateCarousel(smooth = true) {
            const cardWidth = getCardWidth();
            const gap = getCardGap();
            const translateX = -riwayatCurrentIndex * (cardWidth + gap);
            
            riwayatTrack.style.transition = smooth ? 'transform 0.5s ease' : 'none';
            riwayatTrack.style.transform = `translateX(${translateX}px)`;
            
            console.log('Mobile-Fixed Carousel Update - Index:', riwayatCurrentIndex, 'CardWidth:', cardWidth, 'Gap:', gap, 'TranslateX:', translateX);
            
            if (riwayatPrevBtn) {
                riwayatPrevBtn.disabled = false;
                riwayatPrevBtn.style.opacity = '1';
            }
            
            if (riwayatNextBtn) {
                riwayatNextBtn.disabled = false;
                riwayatNextBtn.style.opacity = '1';
            }

            updateIndicatorDots();
        }

        function updateIndicatorDots() {
            const dots = document.querySelectorAll('.auto-play-dot');
            dots.forEach((dot, index) => {
                if (index === riwayatCurrentIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function goToNext() {
            if (isTransitioning) return;
            
            isTransitioning = true;
            riwayatCurrentIndex = (riwayatCurrentIndex + 1) % totalCards;
            updateCarousel();
            
            console.log('Next - New index:', riwayatCurrentIndex);
            
            setTimeout(() => {
                isTransitioning = false;
            }, 500);
        }

        function goToPrev() {
            if (isTransitioning) return;
            
            isTransitioning = true;
            riwayatCurrentIndex = (riwayatCurrentIndex - 1 + totalCards) % totalCards;
            updateCarousel();
            
            console.log('Prev - New index:', riwayatCurrentIndex);
            
            setTimeout(() => {
                isTransitioning = false;
            }, 500);
        }

        function goToSlide(index) {
            if (isTransitioning) return;
            
            isTransitioning = true;
            riwayatCurrentIndex = index % totalCards;
            updateCarousel();
            
            setTimeout(() => {
                isTransitioning = false;
            }, 500);
        }

        function startAutoPlay() {
            stopAutoPlay();
            autoPlayInterval = setInterval(() => {
                if (!isTransitioning && !document.hidden) {
                    goToNext();
                }
            }, 4000);
        }

        function stopAutoPlay() {
            if (autoPlayInterval) {
                clearInterval(autoPlayInterval);
                autoPlayInterval = null;
            }
        }

        if (riwayatPrevBtn) {
            riwayatPrevBtn.addEventListener('click', function() {
                console.log('Prev button clicked');
                stopAutoPlay();
                goToPrev();
                
                this.style.transform = 'translateY(-50%) scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-50%) scale(1)';
                }, 150);
                
                setTimeout(startAutoPlay, 3000);
            });
        }

        if (riwayatNextBtn) {
            riwayatNextBtn.addEventListener('click', function() {
                console.log('Next button clicked');
                stopAutoPlay();
                goToNext();
                
                this.style.transform = 'translateY(-50%) scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-50%) scale(1)';
                }, 150);
                
                setTimeout(startAutoPlay, 3000);
            });
        }

        // Touch/swipe support for riwayat carousel
        let startX = 0;
        let startY = 0;
        let isDragging = false;
        let currentTranslateX = 0;

        riwayatTrack.addEventListener('touchstart', function(e) {
            if (isTransitioning) return;
            
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isDragging = true;
            
            const style = window.getComputedStyle(riwayatTrack);
            const transform = style.transform;
            
            if (transform === 'none') {
                currentTranslateX = 0;
            } else {
                const matrix = transform.match(/matrix\((.+)\)/);
                currentTranslateX = matrix ? parseFloat(matrix[1].split(', ')[4]) : 0;
            }
            
            riwayatTrack.style.transition = 'none';
            stopAutoPlay();
        }, { passive: true });

        riwayatTrack.addEventListener('touchmove', function(e) {
            if (!isDragging || isTransitioning) return;
            
            const currentX = e.touches[0].clientX;
            const currentY = e.touches[0].clientY;
            const diffX = Math.abs(currentX - startX);
            const diffY = Math.abs(currentY - startY);
            
            // Prevent default if horizontal swipe is dominant
            if (diffX > diffY && diffX > 15) {
                e.preventDefault();
                const diff = currentX - startX;
                const maxDrag = getCardWidth() * 0.2;
                const limitedDiff = Math.max(-maxDrag, Math.min(maxDrag, diff));
                const newTranslateX = currentTranslateX + limitedDiff;
                riwayatTrack.style.transform = `translateX(${newTranslateX}px)`;
            }
        }, { passive: false });

        riwayatTrack.addEventListener('touchend', function(e) {
            if (!isDragging || isTransitioning) return;
            isDragging = false;
            
            const endX = e.changedTouches[0].clientX;
            const diff = startX - endX;
            const threshold = 60;
            
            riwayatTrack.style.transition = 'transform 0.5s ease';
            
            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    goToNext();
                } else {
                    goToPrev();
                }
            } else {
                updateCarousel();
            }
            
            setTimeout(startAutoPlay, 3000);
        }, { passive: true });

        // Mouse drag support
        let mouseStartX = 0;
        let isMouseDragging = false;
        let mouseCurrentTranslateX = 0;

        riwayatTrack.addEventListener('mousedown', function(e) {
            if (isTransitioning) return;
            
            mouseStartX = e.clientX;
            isMouseDragging = true;
            
            const style = window.getComputedStyle(riwayatTrack);
            const transform = style.transform;
            
            if (transform === 'none') {
                mouseCurrentTranslateX = 0;
            } else {
                const matrix = transform.match(/matrix\((.+)\)/);
                mouseCurrentTranslateX = matrix ? parseFloat(matrix[1].split(', ')[4]) : 0;
            }
            
            riwayatTrack.style.cursor = 'grabbing';
            riwayatTrack.style.transition = 'none';
            stopAutoPlay();
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isMouseDragging || isTransitioning) return;
            e.preventDefault();
            
            const currentX = e.clientX;
            const diff = currentX - mouseStartX;
            // Limit drag distance
            const maxDrag = getCardWidth() * 0.2;
            const limitedDiff = Math.max(-maxDrag, Math.min(maxDrag, diff));
            const newTranslateX = mouseCurrentTranslateX + limitedDiff;
            
            riwayatTrack.style.transform = `translateX(${newTranslateX}px)`;
        });

        document.addEventListener('mouseup', function(e) {
            if (!isMouseDragging || isTransitioning) return;
            isMouseDragging = false;
            
            riwayatTrack.style.cursor = 'grab';
            riwayatTrack.style.transition = 'transform 0.5s ease';
            
            const endX = e.clientX;
            const diff = mouseStartX - endX;
            const threshold = 50;
            
            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    goToNext();
                } else {
                    goToPrev();
                }
            } else {
                // Snap back to current position
                updateCarousel();
            }
            
            // Restart auto-play after interaction
            setTimeout(startAutoPlay, 3000);
        });

        // Indicator dots click handler
        const dots = document.querySelectorAll('.auto-play-dot');
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                stopAutoPlay();
                goToSlide(index);
                setTimeout(startAutoPlay, 3000);
            });
        });

        // Keyboard navigation for riwayat carousel
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && riwayatPrevBtn) {
                riwayatPrevBtn.click();
            } else if (e.key === 'ArrowRight' && riwayatNextBtn) {
                riwayatNextBtn.click();
            }
        });

        // ENHANCED: Window resize handler
        function handleResize() {
            // Wait for resize to finish
            setTimeout(() => {
                // Update card sizes dynamically for mobile
                if (window.innerWidth <= 580) {
                    const cards = document.querySelectorAll('.carousel-card');
                    const newWidth = getCardWidth();
                    cards.forEach(card => {
                        card.style.minWidth = newWidth + 'px';
                        card.style.maxWidth = newWidth + 'px';
                    });
                }
                
                updateCarousel(false);
                console.log('Carousel resized - Screen:', window.innerWidth, 'Card width:', getCardWidth());
            }, 150);
        }

        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 250);
        });

        // Pause auto-play when page is not visible
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoPlay();
            } else {
                startAutoPlay();
            }
        });

        // Pause auto-play on hover (desktop)
        riwayatTrack.addEventListener('mouseenter', function() {
            stopAutoPlay();
        });

        riwayatTrack.addEventListener('mouseleave', function() {
            if (!isMouseDragging) {
                startAutoPlay();
            }
        });

        // ENHANCED: Initialize mobile carousel
        function initializeMobileCarousel() {
            if (window.innerWidth <= 580) {
                const cards = document.querySelectorAll('.carousel-card');
                const cardWidth = getCardWidth();
                
                cards.forEach(card => {
                    card.style.minWidth = cardWidth + 'px';
                    card.style.maxWidth = cardWidth + 'px';
                    
                    // Ensure see-more-card styling is consistent
                    if (card.classList.contains('see-more-card')) {
                        card.style.background = 'linear-gradient(135deg, #008dda, #00c4f7)';
                        card.style.color = 'white';
                    }
                });
                
                console.log('Mobile carousel initialized - Card width:', cardWidth);
            }
        }

        // Initialize carousel
        initializeMobileCarousel();
        updateCarousel(false);
        riwayatTrack.style.cursor = 'grab';
        
        // Start auto-play
        startAutoPlay();
        
        console.log('Mobile-Fixed Riwayat Carousel initialized successfully');
        console.log('- Total cards:', totalCards);
        console.log('- Auto-play enabled: 4 seconds interval');
        console.log('- Mobile optimizations: enabled');
    } else {
        console.log('Riwayat Carousel elements not found');
    }

    // ====================================================================
    // STATISTICS ANIMATION
    // ====================================================================

    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const animateStats = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                
                statNumbers.forEach(stat => {
                    const finalNumber = parseInt(stat.textContent);
                    let currentNumber = 0;
                    const increment = Math.ceil(finalNumber / 30);
                    
                    const timer = setInterval(() => {
                        currentNumber += increment;
                        if (currentNumber >= finalNumber) {
                            currentNumber = finalNumber;
                            clearInterval(timer);
                        }
                        stat.textContent = currentNumber;
                    }, 50);
                });
                
                observer.unobserve(entry.target);
            }
        });
    };

    // Animate cards on scroll
    const animateCards = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    };

    // Set up observers for statistics
    const statsObserver = new IntersectionObserver(animateStats, observerOptions);
    const cardsObserver = new IntersectionObserver(animateCards, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe statistics section
    const statsSection = document.querySelector('.riwayat-stats');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }

    // Observe non-carousel cards for animation
    const regularCards = document.querySelectorAll('.riwayat-card:not(.carousel-card)');
    regularCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        cardsObserver.observe(card);
    });

    // ====================================================================
    // SMOOTH SCROLL NAVIGATION
    // ====================================================================

    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                e.preventDefault();
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Close sidebar if open (for mobile)
                const sidebar = document.querySelector('.sidebar');
                if (sidebar && sidebar.style.display === 'block') {
                    sidebar.style.display = 'none';
                }
            }
        });
    });

    // ====================================================================
    // BIAYA/PRICE LIST FUNCTIONALITY
    // ====================================================================

    const eventItems = document.querySelectorAll('.event-list li');
    const priceSections = document.querySelectorAll('.price');

    eventItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active-event class from all event items
            eventItems.forEach(event => event.classList.remove('active-event'));

            // Hide all price sections
            priceSections.forEach(price => price.style.display = 'none');

            // Add active-event class to the clicked item
            this.classList.add('active-event');

            // Get the index of the clicked event
            const index = this.getAttribute('data-index');

            // Show all price sections corresponding to the clicked event
            document.querySelectorAll(`[id^="price-${index}"]`).forEach(price => {
                price.style.display = '';
            });
        });
    });

    // ====================================================================
    // TERMS & CONDITIONS OVERLAY
    // ====================================================================

    document.getElementById('termsLink')?.addEventListener('click', function() {
        document.getElementById('termsOverlay').style.display = 'flex';
    });

    document.getElementById('closeOverlay')?.addEventListener('click', function() {
        document.getElementById('termsOverlay').style.display = 'none';
    });

    window.onclick = function(event) {
        const overlay = document.getElementById('termsOverlay');
        if (event.target == overlay) {
            overlay.style.display = 'none';
        }
    }

    // Debug info
    console.log('MainPage JavaScript initialized successfully');
});