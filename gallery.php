<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Glam PhotoBooth Accra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/gallery.css">
</head>
<body>
    <?php include 'views/components/navbar.php'; ?>

    <!-- Page Header -->
    <section class="hero" style="min-height: 400px;">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="accent-line" style="margin: 0 auto 1.5rem;"></div>
            <h1 class="hero-title" style="font-size: 3rem;">Our Gallery</h1>
            <p class="hero-description" style="font-size: 1.25rem;">
                Explore stunning moments we've captured across Ghana's most memorable events
            </p>
        </div>
    </section>

    <!-- Filter Section -->
    <section style="background: white; padding: 2rem 0; box-shadow: var(--shadow-md); position: sticky; top: 60px; z-index: 100;">
        <div class="container">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                <button class="gallery-filter-btn active" data-filter="all">All Events</button>
                <button class="gallery-filter-btn" data-filter="wedding">Weddings</button>
                <button class="gallery-filter-btn" data-filter="corporate">Corporate</button>
                <button class="gallery-filter-btn" data-filter="birthday">Birthdays</button>
                <button class="gallery-filter-btn" data-filter="engagement">Engagements</button>
                <button class="gallery-filter-btn" data-filter="photobooth">Photobooth</button>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="section bg-cream">
        <div class="container">
            <div class="gallery-grid">
                <!-- Wedding Photos -->
                <div class="gallery-item" data-category="wedding">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #FFB6C1, #FFC0CB);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Wedding</span>
                                <h4>Akua & Kwame</h4>
                                <p>Traditional Wedding Ceremony</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="wedding">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #E6E6FA, #D8BFD8);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Wedding</span>
                                <h4>Ama & Yaw</h4>
                                <p>Garden Reception</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="wedding">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #FFDAB9, #FFE4B5);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Wedding</span>
                                <h4>Esi & Kofi</h4>
                                <p>Beach Wedding</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corporate Events -->
                <div class="gallery-item" data-category="corporate">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">
                            <div class="gallery-overlay">
                                <span class="badge badge-navy">Corporate</span>
                                <h4>Tech Summit 2024</h4>
                                <p>Annual Technology Conference</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="corporate">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #2C3E50, #34495E);">
                            <div class="gallery-overlay">
                                <span class="badge badge-navy">Corporate</span>
                                <h4>Banking Awards Gala</h4>
                                <p>Excellence Awards Night</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="corporate">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #16A085, #1ABC9C);">
                            <div class="gallery-overlay">
                                <span class="badge badge-navy">Corporate</span>
                                <h4>Product Launch</h4>
                                <p>New Product Unveiling Event</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Birthday Events -->
                <div class="gallery-item" data-category="birthday">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #FF6B9D, #FFC371);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Birthday</span>
                                <h4>Ama's 30th Birthday</h4>
                                <p>Glamorous Birthday Bash</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="birthday">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #A8E6CF, #DCEDC1);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Birthday</span>
                                <h4>Kids Birthday Party</h4>
                                <p>Colorful Children's Celebration</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="birthday">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #667EEA, #764BA2);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Birthday</span>
                                <h4>50th Milestone</h4>
                                <p>Elegant Golden Jubilee</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Engagement Events -->
                <div class="gallery-item" data-category="engagement">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #F093FB, #F5576C);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Engagement</span>
                                <h4>Sunset Proposal</h4>
                                <p>Beachside Engagement Shoot</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="engagement">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #FFA69E, #FAF3DD);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Engagement</span>
                                <h4>Garden Engagement</h4>
                                <p>Botanical Garden Session</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="engagement">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #C9A9E9, #E7C6FF);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Engagement</span>
                                <h4>Urban Love Story</h4>
                                <p>City Streets Pre-Wedding</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photobooth Events -->
                <div class="gallery-item" data-category="photobooth">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #F7971E, #FFD200);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Photobooth</span>
                                <h4>360° Spin Booth</h4>
                                <p>Corporate Event Experience</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="photobooth">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #FF512F, #DD2476);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Photobooth</span>
                                <h4>Deluxe Photo Booth</h4>
                                <p>Wedding Reception Fun</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-item" data-category="photobooth">
                    <div class="gallery-card">
                        <div class="gallery-image" style="background: linear-gradient(135deg, #11998E, #38EF7D);">
                            <div class="gallery-overlay">
                                <span class="badge badge-gold">Photobooth</span>
                                <h4>Green Screen Magic</h4>
                                <p>Birthday Party Memories</p>
                                <button class="btn btn-primary btn-sm view-gallery-btn">View Gallery</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="text-center" style="margin-top: 3rem;">
                <button class="btn btn-outline btn-lg" id="loadMoreBtn">Load More Events</button>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section bg-white">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-3" style="text-align: center; margin-bottom: 2rem;">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--gold-primary); margin-bottom: 0.5rem;">500+</div>
                    <p style="color: var(--medium-gray); font-size: 1.125rem;">Events Captured</p>
                </div>
                <div class="col-12 col-md-3" style="text-align: center; margin-bottom: 2rem;">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--gold-primary); margin-bottom: 0.5rem;">50K+</div>
                    <p style="color: var(--medium-gray); font-size: 1.125rem;">Photos Delivered</p>
                </div>
                <div class="col-12 col-md-3" style="text-align: center; margin-bottom: 2rem;">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--gold-primary); margin-bottom: 0.5rem;">1000+</div>
                    <p style="color: var(--medium-gray); font-size: 1.125rem;">Happy Clients</p>
                </div>
                <div class="col-12 col-md-3" style="text-align: center; margin-bottom: 2rem;">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--gold-primary); margin-bottom: 0.5rem;">50+</div>
                    <p style="color: var(--medium-gray); font-size: 1.125rem;">Service Providers</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section" style="background: linear-gradient(135deg, var(--navy-dark), var(--navy-medium)); color: white; text-align: center;">
        <div class="container">
            <h2 style="color: white; margin-bottom: 1rem;">Want Your Event Featured Here?</h2>
            <p style="font-size: 1.125rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem;">
                Book one of our premium packages and join our portfolio of stunning events
            </p>
            <a href="packages.php" class="btn btn-primary btn-lg">Browse Packages</a>
        </div>
    </section>

    <?php include 'views/components/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/gallery.js"></script>
</body>
</html>
