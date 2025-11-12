<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glam PhotoBooth Accra - Luxury Photography Services</title>
    <meta name="description" content="Ghana's premier photography services marketplace. Professional photographers and luxury photobooth experiences for your special moments.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'views/components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="accent-line" style="margin: 0 auto 1.5rem;"></div>
            <h1 class="hero-title">Capture Your Moments<br>In <span style="color: var(--gold-primary);">Luxury</span></h1>
            <p class="hero-subtitle">Ghana's Premier Photography Services Marketplace</p>
            <p class="hero-description">
                Connect with professional photographers and rent luxury photobooths for weddings,
                corporate events, birthdays, and every special occasion worth remembering.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem;">
                <a href="packages.php" class="btn btn-primary btn-lg">Browse Packages</a>
                <a href="gallery.php" class="btn btn-outline-gold btn-lg">View Gallery</a>
            </div>
        </div>
    </section>

    <!-- Services Overview Section -->
    <section class="section bg-white">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div class="accent-line" style="margin: 0 auto 1rem;"></div>
                <h2>Our Premium Services</h2>
                <p style="font-size: 1.125rem; color: var(--medium-gray); max-width: 700px; margin: 0 auto;">
                    From professional photographers to state-of-the-art photobooths, we bring luxury to every frame
                </p>
            </div>

            <div class="row">
                <!-- Photography Service -->
                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card card-luxury" style="text-align: center; height: 100%;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, var(--gold-primary), var(--gold-accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                            📸
                        </div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Professional Photography</h3>
                        <p style="color: var(--medium-gray); line-height: 1.8;">
                            Award-winning photographers capturing your most precious moments with artistic excellence and attention to detail.
                        </p>
                        <a href="packages.php#photography" class="btn btn-outline" style="margin-top: 1.5rem;">Explore Photographers</a>
                    </div>
                </div>

                <!-- Photobooth Rental -->
                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card card-luxury" style="text-align: center; height: 100%; position: relative;">
                        <span class="badge badge-gold" style="position: absolute; top: 20px; right: 20px;">Popular</span>
                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, var(--navy-dark), var(--navy-medium)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                            🎭
                        </div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Luxury Photobooths</h3>
                        <p style="color: var(--medium-gray); line-height: 1.8;">
                            Interactive photobooth experiences with instant prints, digital sharing, custom backdrops, and fun props.
                        </p>
                        <a href="packages.php#photobooth" class="btn btn-primary" style="margin-top: 1.5rem;">Browse Photobooths</a>
                    </div>
                </div>

                <!-- Event Add-ons -->
                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card card-luxury" style="text-align: center; height: 100%;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, var(--gold-primary), var(--gold-accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                            ✨
                        </div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Premium Add-ons</h3>
                        <p style="color: var(--medium-gray); line-height: 1.8;">
                            Elevate your event with videography, drone coverage, same-day edits, photo albums, and more.
                        </p>
                        <a href="packages.php#addons" class="btn btn-outline" style="margin-top: 1.5rem;">View Add-ons</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="section bg-cream">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div class="accent-line" style="margin: 0 auto 1rem;"></div>
                <h2>How It Works</h2>
                <p style="font-size: 1.125rem; color: var(--medium-gray);">
                    Booking luxury photography services has never been easier
                </p>
            </div>

            <div class="row">
                <div class="col-12 col-md-3" style="margin-bottom: 2rem; text-align: center;">
                    <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: var(--gold-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem;">1</div>
                    <h4>Browse Services</h4>
                    <p style="color: var(--medium-gray);">Explore our curated selection of photographers and photobooths</p>
                </div>

                <div class="col-12 col-md-3" style="margin-bottom: 2rem; text-align: center;">
                    <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: var(--gold-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem;">2</div>
                    <h4>Select Package</h4>
                    <p style="color: var(--medium-gray);">Choose the perfect package and customize with add-ons</p>
                </div>

                <div class="col-12 col-md-3" style="margin-bottom: 2rem; text-align: center;">
                    <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: var(--gold-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem;">3</div>
                    <h4>Secure Payment</h4>
                    <p style="color: var(--medium-gray);">Complete your booking with our secure payment system</p>
                </div>

                <div class="col-12 col-md-3" style="margin-bottom: 2rem; text-align: center;">
                    <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: var(--gold-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem;">4</div>
                    <h4>Enjoy Your Event</h4>
                    <p style="color: var(--medium-gray);">Relax while professionals capture your special moments</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Packages Section -->
    <section class="section bg-white">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div class="accent-line" style="margin: 0 auto 1rem;"></div>
                <h2>Featured Packages</h2>
                <p style="font-size: 1.125rem; color: var(--medium-gray);">
                    Our most popular photography and photobooth packages
                </p>
            </div>

            <div class="row">
                <!-- Basic Package -->
                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <h4 style="color: var(--navy-dark); margin-bottom: 0.5rem;">Starter</h4>
                        <div style="font-size: 2.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                            GH₵ 1,500
                        </div>
                        <p style="color: var(--medium-gray); margin-bottom: 2rem;">Perfect for intimate gatherings</p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 2rem; text-align: left;">
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 3 Hours Coverage</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 1 Photographer</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 200+ Edited Photos</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Online Gallery</li>
                            <li style="padding: 0.5rem 0;">✓ 7-Day Delivery</li>
                        </ul>
                        <a href="packages.php?package=starter" class="btn btn-outline">Select Package</a>
                    </div>
                </div>

                <!-- Premium Package -->
                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card card-luxury" style="text-align: center; padding: 2rem; position: relative; transform: scale(1.05); box-shadow: var(--shadow-xl);">
                        <span class="badge-recommended">Recommended</span>
                        <h4 style="color: var(--navy-dark); margin-bottom: 0.5rem;">Premium</h4>
                        <div style="font-size: 2.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                            GH₵ 3,500
                        </div>
                        <p style="color: var(--medium-gray); margin-bottom: 2rem;">Most popular choice</p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 2rem; text-align: left;">
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 6 Hours Coverage</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 2 Photographers</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Luxury Photobooth</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 500+ Edited Photos</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Same-Day Highlights</li>
                            <li style="padding: 0.5rem 0;">✓ 3-Day Delivery</li>
                        </ul>
                        <a href="packages.php?package=premium" class="btn btn-primary">Select Package</a>
                    </div>
                </div>

                <!-- Luxury Package -->
                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <h4 style="color: var(--navy-dark); margin-bottom: 0.5rem;">Luxury</h4>
                        <div style="font-size: 2.5rem; font-weight: 700; color: var(--gold-primary); margin: 1rem 0;">
                            GH₵ 6,000
                        </div>
                        <p style="color: var(--medium-gray); margin-bottom: 2rem;">Ultimate experience</p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 2rem; text-align: left;">
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Full Day Coverage</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ 3 Photographers</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Premium Photobooth</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Videography + Drone</li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">✓ Unlimited Photos</li>
                            <li style="padding: 0.5rem 0;">✓ 48-Hour Delivery</li>
                        </ul>
                        <a href="packages.php?package=luxury" class="btn btn-outline">Select Package</a>
                    </div>
                </div>
            </div>

            <div class="text-center" style="margin-top: 2rem;">
                <a href="packages.php" class="btn btn-secondary btn-lg">View All Packages</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section bg-cream">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div class="accent-line" style="margin: 0 auto 1rem;"></div>
                <h2>What Our Clients Say</h2>
                <p style="font-size: 1.125rem; color: var(--medium-gray);">
                    Join thousands of satisfied customers across Ghana
                </p>
            </div>

            <div class="row">
                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card" style="padding: 2rem;">
                        <div style="color: var(--gold-primary); font-size: 2rem; margin-bottom: 1rem;">★★★★★</div>
                        <p style="font-style: italic; margin-bottom: 1.5rem; color: var(--dark-gray);">
                            "Absolutely stunning work! The photographers were professional, creative, and captured every moment perfectly. Our wedding photos exceeded all expectations!"
                        </p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; background: var(--gold-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">AK</div>
                            <div>
                                <strong>Akua Mensah</strong><br>
                                <small style="color: var(--medium-gray);">Wedding Client</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card" style="padding: 2rem;">
                        <div style="color: var(--gold-primary); font-size: 2rem; margin-bottom: 1rem;">★★★★★</div>
                        <p style="font-style: italic; margin-bottom: 1.5rem; color: var(--dark-gray);">
                            "The photobooth was a huge hit at our corporate event! Guests loved the instant prints and the team was incredibly organized. Highly recommend!"
                        </p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; background: var(--navy-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">KO</div>
                            <div>
                                <strong>Kwame Osei</strong><br>
                                <small style="color: var(--medium-gray);">Corporate Event</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                    <div class="card" style="padding: 2rem;">
                        <div style="color: var(--gold-primary); font-size: 2rem; margin-bottom: 1rem;">★★★★★</div>
                        <p style="font-style: italic; margin-bottom: 1.5rem; color: var(--dark-gray);">
                            "From booking to delivery, everything was seamless. The platform made it so easy to find the perfect photographer for our birthday celebration!"
                        </p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; background: var(--gold-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">EA</div>
                            <div>
                                <strong>Esi Agyeman</strong><br>
                                <small style="color: var(--medium-gray);">Birthday Event</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section" style="background: linear-gradient(135deg, var(--navy-dark), var(--navy-medium)); color: white; text-align: center;">
        <div class="container">
            <div class="accent-line" style="margin: 0 auto 1.5rem;"></div>
            <h2 style="color: white; font-size: 2.5rem; margin-bottom: 1rem;">Ready to Capture Your Moments?</h2>
            <p style="font-size: 1.25rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem; max-width: 700px; margin-left: auto; margin-right: auto;">
                Join Ghana's leading photography services marketplace and connect with top professionals today
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="register.php" class="btn btn-primary btn-lg">Get Started Free</a>
                <a href="provider-signup.php" class="btn btn-outline-gold btn-lg">Become a Provider</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'views/components/footer.php'; ?>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>
