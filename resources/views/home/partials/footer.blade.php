<footer id="footer" class="footer">

  <div class="footer-newsletter">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-6">
          <h4>Join Our Newsletter</h4>
          <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
          <form action="#" method="post" class="php-email-form">
            @csrf
            <div class="newsletter-form">
              <input type="email" name="email" placeholder="Enter your email" required>
              <input type="submit" value="Subscribe">
            </div>
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Your subscription request has been sent. Thank you!</div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6 footer-about">
        <a href="{{ route('home') }}" class="d-flex align-items-center">
          <span class="sitename">{{ $siteInfo['name'] ?? 'BizLand' }}</span>
        </a>
        <div class="footer-contact pt-3">
          <p>{{ $siteInfo['address_line1'] ?? 'A108 Adam Street' }}</p>
          <p>{{ $siteInfo['address_line2'] ?? 'New York, NY 535022' }}</p>
          <p class="mt-3"><strong>Phone:</strong> <span>{{ $siteInfo['phone'] ?? '+1 5589 55488 55' }}</span></p>
          <p><strong>Email:</strong> <span>{{ $siteInfo['email'] ?? 'info@example.com' }}</span></p>
        </div>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Useful Links</h4>
        <ul>
          <li><i class="bi bi-chevron-right"></i> <a href="#hero">Home</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#about">About us</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#services">Services</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#">Terms of service</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#">Privacy policy</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Our Services</h4>
        <ul>
          <li><i class="bi bi-chevron-right"></i> <a href="#">Web Design</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#">Web Development</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#">Product Management</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#">Marketing</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="#">Graphic Design</a></li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-12">
        <h4>Follow Us</h4>
        <p>{{ $siteInfo['about_short'] ?? 'Cras fermentum odio eu feugiat lide par naso tierra videa magna derita valies' }}</p>
        <div class="social-links d-flex">
          @if(!empty($siteInfo['social_links']['twitter']))
            <a href="{{ $siteInfo['social_links']['twitter'] }}"><i class="bi bi-twitter-x"></i></a>
          @endif
          @if(!empty($siteInfo['social_links']['facebook']))
            <a href="{{ $siteInfo['social_links']['facebook'] }}"><i class="bi bi-facebook"></i></a>
          @endif
          @if(!empty($siteInfo['social_links']['instagram']))
            <a href="{{ $siteInfo['social_links']['instagram'] }}"><i class="bi bi-instagram"></i></a>
          @endif
          @if(!empty($siteInfo['social_links']['linkedin']))
            <a href="{{ $siteInfo['social_links']['linkedin'] }}"><i class="bi bi-linkedin"></i></a>
          @endif
        </div>
      </div>

    </div>
  </div>

  <div class="container copyright text-center mt-4">
    <p>© <span>Copyright</span> <strong class="px-1 sitename">{{ $siteInfo['name'] ?? 'BizLand' }}</strong> <span>All Rights Reserved</span></p>
    <div class="credits">
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </div>

</footer>
