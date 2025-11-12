<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages - Glam PhotoBooth Accra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
</head>
<body>
    <?php include 'views/components/navbar.php'; ?>

    <!-- Page Header -->
    <section class="hero" style="min-height: 400px;">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="accent-line" style="margin: 0 auto 1.5rem;"></div>
            <h1 class="hero-title" style="font-size: 3rem;">Our Packages</h1>
            <p class="hero-description" style="font-size: 1.25rem;">
                Choose from our curated selection of photography services and luxury photobooth experiences
            </p>
        </div>
    </section>

    <!-- Filter & Search Section -->
    <section style="background: white; padding: 2rem 0; box-shadow: var(--shadow-md); position: sticky; top: 60px; z-index: 100;">
        <div class="container">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                <!-- Category Filter -->
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button class="filter-btn active" data-filter="all">All Services</button>
                    <button class="filter-btn" data-filter="photography">Photography</button>
                    <button class="filter-btn" data-filter="photobooth">Photobooths</button>
                    <button class="filter-btn" data-filter="addons">Add-ons</button>
                </div>

                <!-- Search -->
                <div style="flex: 1; max-width: 300px;">
                    <input type="text" class="form-control" placeholder="Search packages..." id="searchPackages">
                </div>
            </div>
        </div>
    </section>

    <!-- Photography Packages -->
    <section class="section bg-cream" id="photography">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div class="accent-line" style="margin: 0 auto 1rem;"></div>
                <h2>Photography Packages</h2>
                <p style="font-size: 1.125rem; color: var(--medium-gray);">
                    Professional photographers for every occasion
                </p>
            </div>

            <div class="row">
                <!-- Starter Photography -->
                <div class="col-12 col-md-4 package-item" data-category="photography" style="margin-bottom: 2rem;">
                    <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                        <div style="height: 200px; background: linear-gradient(135deg, var(--navy-dark), var(--navy-medium)); border-radius: var(--radius-md) var(--radius-md) 0 0; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin: -1.5rem -1.5rem 1.5rem;">
                            📸
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Starter Photography</h3>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                                GH₵ 1,500
                            </div>
                            <p style="color: var(--medium-gray); margin-bottom: 1.5rem;">Perfect for small events and intimate gatherings</p>

                            <div style="flex: 1;">
                                <h5 style="font-size: 0.875rem; text-transform: uppercase; color: var(--navy-dark); margin-bottom: 1rem;">What's Included:</h5>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 3 Hours Coverage</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 1 Professional Photographer</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 200+ Edited Photos</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Online Gallery Access</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ High-Resolution Downloads</li>
                                    <li style="padding: 0.5rem 0;">✓ 7-Day Delivery</li>
                                </ul>
                            </div>

                            <a href="booking.php?package=photography-starter" class="btn btn-outline" style="margin-top: 1.5rem; width: 100%;">Book Now</a>
                        </div>
                    </div>
                </div>

                <!-- Premium Photography -->
                <div class="col-12 col-md-4 package-item" data-category="photography" style="margin-bottom: 2rem;">
                    <div class="card card-luxury" style="height: 100%; display: flex; flex-direction: column; position: relative;">
                        <span class="badge-recommended">Recommended</span>
                        <div style="height: 200px; background: linear-gradient(135deg, var(--gold-primary), var(--gold-accent)); border-radius: var(--radius-md) var(--radius-md) 0 0; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin: -1.5rem -1.5rem 1.5rem;">
                            📷
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Premium Photography</h3>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                                GH₵ 3,500
                            </div>
                            <p style="color: var(--medium-gray); margin-bottom: 1.5rem;">Our most popular photography package</p>

                            <div style="flex: 1;">
                                <h5 style="font-size: 0.875rem; text-transform: uppercase; color: var(--navy-dark); margin-bottom: 1rem;">What's Included:</h5>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 6 Hours Coverage</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 2 Professional Photographers</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 500+ Edited Photos</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Same-Day Highlights (50 photos)</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Online Gallery + USB Drive</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Premium Photo Album (20 pages)</li>
                                    <li style="padding: 0.5rem 0;">✓ 3-Day Full Delivery</li>
                                </ul>
                            </div>

                            <a href="booking.php?package=photography-premium" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%;">Book Now</a>
                        </div>
                    </div>
                </div>

                <!-- Luxury Photography -->
                <div class="col-12 col-md-4 package-item" data-category="photography" style="margin-bottom: 2rem;">
                    <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                        <div style="height: 200px; background: linear-gradient(135deg, #000, #333); border-radius: var(--radius-md) var(--radius-md) 0 0; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin: -1.5rem -1.5rem 1.5rem;">
                            👑
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Luxury Photography</h3>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                                GH₵ 6,000
                            </div>
                            <p style="color: var(--medium-gray); margin-bottom: 1.5rem;">The ultimate photography experience</p>

                            <div style="flex: 1;">
                                <h5 style="font-size: 0.875rem; text-transform: uppercase; color: var(--navy-dark); margin-bottom: 1rem;">What's Included:</h5>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Full Day Coverage (12 hours)</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 3 Professional Photographers</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Unlimited Edited Photos</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 4K Videography</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Drone Aerial Footage</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Luxury Photo Album (40 pages)</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Same-Day Edits Video</li>
                                    <li style="padding: 0.5rem 0;">✓ 48-Hour Full Delivery</li>
                                </ul>
                            </div>

                            <a href="booking.php?package=photography-luxury" class="btn btn-outline" style="margin-top: 1.5rem; width: 100%;">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Photobooth Packages -->
    <section class="section bg-white" id="photobooth">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div class="accent-line" style="margin: 0 auto 1rem;"></div>
                <h2>Photobooth Rentals</h2>
                <p style="font-size: 1.125rem; color: var(--medium-gray);">
                    Interactive entertainment with instant memories
                </p>
            </div>

            <div class="row">
                <!-- Basic Photobooth -->
                <div class="col-12 col-md-4 package-item" data-category="photobooth" style="margin-bottom: 2rem;">
                    <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                        <div style="height: 200px; background: linear-gradient(135deg, #FF6B6B, #FF8E53); border-radius: var(--radius-md) var(--radius-md) 0 0; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin: -1.5rem -1.5rem 1.5rem;">
                            🎭
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Classic Photobooth</h3>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                                GH₵ 1,200
                            </div>
                            <p style="color: var(--medium-gray); margin-bottom: 1.5rem;">Fun and affordable photobooth fun</p>

                            <div style="flex: 1;">
                                <h5 style="font-size: 0.875rem; text-transform: uppercase; color: var(--navy-dark); margin-bottom: 1rem;">What's Included:</h5>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 3 Hours Rental</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Unlimited Photo Sessions</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Instant Prints (4x6)</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Digital Copies</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Fun Props Collection</li>
                                    <li style="padding: 0.5rem 0;">✓ Attendant Included</li>
                                </ul>
                            </div>

                            <a href="booking.php?package=photobooth-classic" class="btn btn-outline" style="margin-top: 1.5rem; width: 100%;">Book Now</a>
                        </div>
                    </div>
                </div>

                <!-- Premium Photobooth -->
                <div class="col-12 col-md-4 package-item" data-category="photobooth" style="margin-bottom: 2rem;">
                    <div class="card card-luxury" style="height: 100%; display: flex; flex-direction: column; position: relative;">
                        <span class="badge badge-gold" style="position: absolute; top: 10px; right: 10px;">Popular</span>
                        <div style="height: 200px; background: linear-gradient(135deg, var(--gold-primary), var(--gold-accent)); border-radius: var(--radius-md) var(--radius-md) 0 0; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin: -1.5rem -1.5rem 1.5rem;">
                            ✨
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Deluxe Photobooth</h3>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                                GH₵ 2,500
                            </div>
                            <p style="color: var(--medium-gray); margin-bottom: 1.5rem;">Enhanced features for memorable events</p>

                            <div style="flex: 1;">
                                <h5 style="font-size: 0.875rem; text-transform: uppercase; color: var(--navy-dark); margin-bottom: 1rem;">What's Included:</h5>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 5 Hours Rental</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Unlimited Sessions</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Premium Prints (6x8)</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Custom Backdrop Design</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Green Screen Options</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ GIF & Boomerang Creation</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Social Media Sharing</li>
                                    <li style="padding: 0.5rem 0;">✓ Premium Props & Costumes</li>
                                </ul>
                            </div>

                            <a href="booking.php?package=photobooth-deluxe" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%;">Book Now</a>
                        </div>
                    </div>
                </div>

                <!-- Luxury 360 Photobooth -->
                <div class="col-12 col-md-4 package-item" data-category="photobooth" style="margin-bottom: 2rem;">
                    <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                        <div style="height: 200px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: var(--radius-md) var(--radius-md) 0 0; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin: -1.5rem -1.5rem 1.5rem;">
                            🎬
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">360° Spin Booth</h3>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                                GH₵ 4,500
                            </div>
                            <p style="color: var(--medium-gray); margin-bottom: 1.5rem;">Revolutionary 360-degree video experience</p>

                            <div style="flex: 1;">
                                <h5 style="font-size: 0.875rem; text-transform: uppercase; color: var(--navy-dark); margin-bottom: 1rem;">What's Included:</h5>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 4 Hours Rental</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 360° Rotating Camera</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Slow-Motion Capability</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Custom Video Overlays</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Instant Sharing (QR Code)</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Professional Lighting</li>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Red Carpet Experience</li>
                                    <li style="padding: 0.5rem 0;">✓ 2 Attendants Included</li>
                                </ul>
                            </div>

                            <a href="booking.php?package=photobooth-360" class="btn btn-outline" style="margin-top: 1.5rem; width: 100%;">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add-ons Section -->
    <section class="section bg-cream" id="addons">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div class="accent-line" style="margin: 0 auto 1rem;"></div>
                <h2>Premium Add-ons</h2>
                <p style="font-size: 1.125rem; color: var(--medium-gray);">
                    Enhance your package with these additional services
                </p>
            </div>

            <div class="row">
                <!-- Add-on Cards -->
                <div class="col-12 col-md-3 package-item" data-category="addons" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🎥</div>
                        <h4>Videography</h4>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">+ GH₵ 2,000</div>
                        <p style="color: var(--medium-gray); font-size: 0.875rem;">Professional 4K video coverage with cinematic editing</p>
                        <button class="btn btn-outline btn-sm" style="margin-top: 1rem;">Add to Package</button>
                    </div>
                </div>

                <div class="col-12 col-md-3 package-item" data-category="addons" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🚁</div>
                        <h4>Drone Coverage</h4>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">+ GH₵ 800</div>
                        <p style="color: var(--medium-gray); font-size: 0.875rem;">Stunning aerial shots and video footage</p>
                        <button class="btn btn-outline btn-sm" style="margin-top: 1rem;">Add to Package</button>
                    </div>
                </div>

                <div class="col-12 col-md-3 package-item" data-category="addons" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📖</div>
                        <h4>Photo Album</h4>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">+ GH₵ 600</div>
                        <p style="color: var(--medium-gray); font-size: 0.875rem;">Premium leather-bound album (30 pages)</p>
                        <button class="btn btn-outline btn-sm" style="margin-top: 1rem;">Add to Package</button>
                    </div>
                </div>

                <div class="col-12 col-md-3 package-item" data-category="addons" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">⚡</div>
                        <h4>Same-Day Edit</h4>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">+ GH₵ 1,500</div>
                        <p style="color: var(--medium-gray); font-size: 0.875rem;">Highlight video delivered during your event</p>
                        <button class="btn btn-outline btn-sm" style="margin-top: 1rem;">Add to Package</button>
                    </div>
                </div>

                <div class="col-12 col-md-3 package-item" data-category="addons" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🖼️</div>
                        <h4>Canvas Prints</h4>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">+ GH₵ 400</div>
                        <p style="color: var(--medium-gray); font-size: 0.875rem;">Gallery-quality canvas print (24x36 inches)</p>
                        <button class="btn btn-outline btn-sm" style="margin-top: 1rem;">Add to Package</button>
                    </div>
                </div>

                <div class="col-12 col-md-3 package-item" data-category="addons" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">💍</div>
                        <h4>Pre-Wedding Shoot</h4>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">+ GH₵ 1,200</div>
                        <p style="color: var(--medium-gray); font-size: 0.875rem;">2-hour engagement/pre-wedding photo session</p>
                        <button class="btn btn-outline btn-sm" style="margin-top: 1rem;">Add to Package</button>
                    </div>
                </div>

                <div class="col-12 col-md-3 package-item" data-category="addons" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">💿</div>
                        <h4>USB + Cloud Storage</h4>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">+ GH₵ 200</div>
                        <p style="color: var(--medium-gray); font-size: 0.875rem;">Custom USB drive + 1-year cloud backup</p>
                        <button class="btn btn-outline btn-sm" style="margin-top: 1rem;">Add to Package</button>
                    </div>
                </div>

                <div class="col-12 col-md-3 package-item" data-category="addons" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">⏱️</div>
                        <h4>Extended Hours</h4>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">+ GH₵ 500/hr</div>
                        <p style="color: var(--medium-gray); font-size: 0.875rem;">Extend your coverage beyond package hours</p>
                        <button class="btn btn-outline btn-sm" style="margin-top: 1rem;">Add to Package</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section" style="background: linear-gradient(135deg, var(--navy-dark), var(--navy-medium)); color: white; text-align: center;">
        <div class="container">
            <h2 style="color: white; margin-bottom: 1rem;">Need a Custom Package?</h2>
            <p style="font-size: 1.125rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem;">
                Contact us to create a personalized package that perfectly fits your event needs
            </p>
            <a href="contact.php" class="btn btn-primary btn-lg">Get Custom Quote</a>
        </div>
    </section>

    <?php include 'views/components/footer.php'; ?>

    <script src="assets/js/packages.js"></script>
</body>
</html>

<style>
.filter-btn {
    padding: 0.625rem 1.5rem;
    background-color: transparent;
    color: var(--navy-dark);
    border: 2px solid var(--light-gray);
    border-radius: var(--radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.filter-btn:hover,
.filter-btn.active {
    background-color: var(--gold-primary);
    border-color: var(--gold-primary);
    color: var(--white);
}

.package-item {
    transition: opacity var(--transition-normal), transform var(--transition-normal);
}

.package-item.hidden {
    display: none;
}
</style>
