@extends('home.layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'Selamat datang di BizLand Smart Service')

@section('content')

  <!-- Hero Section -->
  <section id="hero" class="hero section light-background">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
          <h1>Welcome to <span>{{ $siteInfo['name'] ?? 'BizLand' }}</span></h1>
          <p>{{ $siteInfo['hero_tagline'] ?? 'We are team of talented designers making websites with Bootstrap' }}</p>
          <div class="d-flex">
            <a href="#about" class="btn-get-started">Get Started</a>
            @if(!empty($siteInfo['video_url']))
              <a href="{{ $siteInfo['video_url'] }}" class="glightbox btn-watch-video d-flex align-items-center">
                <i class="bi bi-play-circle"></i><span>Watch Video</span>
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section><!-- /Hero Section -->

  <!-- Featured Services Section -->
  @if(!empty($featuredServices) && count($featuredServices) > 0)
    <section id="featured-services" class="featured-services section">
      <div class="container">
        <div class="row gy-4">
          @foreach($featuredServices as $index => $item)
            @include('home.components.featured-service', [
              'icon' => $item['icon'],
              'title' => $item['title'],
              'description' => $item['description'],
              'link' => $item['link'] ?? '#',
              'delay' => ($index + 1) * 100
            ])
          @endforeach
        </div>
      </div>
    </section>
  @endif<!-- /Featured Services Section -->

  <!-- About Section -->
  <section id="about" class="about section light-background">
    @include('home.components.section-title', [
      'subtitle' => 'About',
      'title' => 'Find Out More About Us',
      'description' => 'Ut possimus qui ut temporibus culpa velit eveniet modi omnis est ad intellego'
    ])

    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
          <h3>Voluptatem dignis simos provident quasi corporis voluptates sit assumenda.</h3>
          <img src="{{ asset('assets/home/img/about.jpg') }}" class="img-fluid rounded-4 mb-4" alt="About Image">
          <p>Ut fuga quo a eum error eos ut sit corrupti. Sit corrupti maiores autem. Id nihil ad quia superiorem soluta est quo eos accusamus. Soluta quo quas est est. Quo eaque sit distinctio quo corrupti.</p>
          <p>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
          <div class="content ps-0 ps-lg-5">
            <p class="fst-italic">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>
            <ul>
              <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
              <li><i class="bi bi-check-circle-fill"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit esse.</span></li>
              <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li>
            </ul>
            <p>
              Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident
            </p>
            <div class="position-relative mt-4">
              <img src="{{ asset('assets/home/img/about-2.jpg') }}" class="img-fluid rounded-4" alt="">
              @if(!empty($siteInfo['video_url']))
                <a href="{{ $siteInfo['video_url'] }}" class="glightbox pulsating-play-btn"></a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section><!-- /About Section -->

  <!-- Stats Section -->
  @if(!empty($stats) && count($stats) > 0)
    <section id="stats" class="stats section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
          @foreach($stats as $stat)
            @include('home.components.stat-card', [
              'icon' => $stat['icon'],
              'count' => $stat['count'],
              'label' => $stat['label']
            ])
          @endforeach
        </div>
      </div>
    </section>
  @endif<!-- /Stats Section -->

  <!-- Services Section -->
  <section id="services" class="services section">
    @include('home.components.section-title', [
      'subtitle' => 'Services',
      'title' => 'Check our Services',
      'description' => 'Ut possimus qui ut temporibus culpa velit eveniet modi omnis est ad intellego'
    ])

    <div class="container">
      <div class="row gy-4">
        @forelse($services as $index => $service)
          @include('home.components.service-card', [
            'icon' => $service['icon'],
            'title' => $service['title'],
            'description' => $service['description'],
            'link' => $service['link'] ?? '#',
            'delay' => ($index + 1) * 100
          ])
        @empty
          <div class="col-12 text-center">
            <p>Belum ada layanan yang tersedia saat ini.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section><!-- /Services Section -->

  <!-- Team Section -->
  <section id="team" class="team section light-background">
    @include('home.components.section-title', [
      'subtitle' => 'Team',
      'title' => 'Our Hard Working Team',
      'description' => 'Ut possimus qui ut temporibus culpa velit eveniet modi omnis est ad intellego'
    ])

    <div class="container">
      <div class="row gy-4">
        @forelse($teams as $index => $member)
          @include('home.components.team-card', [
            'image' => $member['image'],
            'name' => $member['name'],
            'position' => $member['position'],
            'socials' => $member['socials'] ?? [],
            'delay' => ($index + 1) * 100
          ])
        @empty
          <div class="col-12 text-center">
            <p>Belum ada informasi tim saat ini.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section><!-- /Team Section -->

  <!-- Contact Section -->
  <section id="contact" class="contact section">
    @include('home.components.section-title', [
      'subtitle' => 'Contact',
      'title' => 'Contact Us',
      'description' => 'Ut possimus qui ut temporibus culpa velit eveniet modi omnis est ad intellego'
    ])

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4">
        <div class="col-lg-6">
          <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
            <i class="bi bi-geo-alt"></i>
            <h3>Address</h3>
            <p>{{ $siteInfo['address_line1'] ?? 'A108 Adam Street' }}, {{ $siteInfo['address_line2'] ?? 'New York, NY 535022' }}</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
            <i class="bi bi-telephone"></i>
            <h3>Call Us</h3>
            <p>{{ $siteInfo['phone'] ?? '+1 5589 55488 55' }}</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
            <i class="bi bi-envelope"></i>
            <h3>Email Us</h3>
            <p>{{ $siteInfo['email'] ?? 'info@example.com' }}</p>
          </div>
        </div>
      </div>

      <div class="row gy-4 mt-1">
        <div class="col-lg-6">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d12097.433213460943!2d-74.006227!3d40.710128!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb89d1fe6bc499443!2sDowntown+Conference+Center!5e0!3m2!1sen!2sus!4v1395690623253" 
            frameborder="0" style="border:0; width: 100%; height: 384px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>

        <div class="col-lg-6">
          <form action="#" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="500">
            @csrf
            <div class="row gy-4">
              <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
              </div>
              <div class="col-md-6">
                <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
              </div>
              <div class="col-md-12">
                <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
              </div>
              <div class="col-md-12">
                <textarea class="form-control" name="message" rows="6" placeholder="Message" required=""></textarea>
              </div>
              <div class="col-md-12 text-center">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
                <button type="submit">Send Message</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section><!-- /Contact Section -->

@endsection
