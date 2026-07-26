<div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="{{ $delay ?? 100 }}">
  <div class="team-member">
    <div class="member-img">
      <img src="{{ asset($image ?? 'assets/home/img/team/team-1.jpg') }}" class="img-fluid" alt="{{ $name ?? 'Team Member' }}">
      <div class="social">
        @if(!empty($socials['twitter']))
          <a href="{{ $socials['twitter'] }}"><i class="bi bi-twitter-x"></i></a>
        @endif
        @if(!empty($socials['facebook']))
          <a href="{{ $socials['facebook'] }}"><i class="bi bi-facebook"></i></a>
        @endif
        @if(!empty($socials['instagram']))
          <a href="{{ $socials['instagram'] }}"><i class="bi bi-instagram"></i></a>
        @endif
        @if(!empty($socials['linkedin']))
          <a href="{{ $socials['linkedin'] }}"><i class="bi bi-linkedin"></i></a>
        @endif
      </div>
    </div>
    <div class="member-info">
      <h4>{{ $name ?? 'Member Name' }}</h4>
      <span>{{ $position ?? 'Role' }}</span>
    </div>
  </div>
</div>
