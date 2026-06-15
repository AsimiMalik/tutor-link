<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Brilliance | Connecting Parents With Trusted Tutors</title>


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

<?php
include 'includes/navbar.php';?>
<section class="hero">

  <div class="hero-overlay"></div>

  <div class="container hero-content">

    <div class="hero-text">

      <div class="hero-badge">
        <i class="fa-solid fa-graduation-cap"></i>
        Trusted Nigerian Tutors
      </div>

      <h1>
        Connecting Parents With
        <span>Qualified Tutors</span>
      </h1>

      <p>
        Brilliance helps parents discover verified lesson teachers,
        compare tutor profiles, book sessions, and support better
        academic performance for students.
      </p>

      <div class="hero-buttons">
        <a href="auth/login.php" class="btn-primary">Find Tutors</a>
        <a href="auth/register.php" class="btn-secondary">Become a Tutor</a>
      </div>

      <div class="hero-stats">

        <div class="stat">
          <h3>500+</h3>
          <p>Tutors</p>
        </div>

        <div class="stat">
          <h3>2,000+</h3>
          <p>Students</p>
        </div>

        <div class="stat">
          <h3>4.9★</h3>
          <p>Average Rating</p>
        </div>

      </div>

    </div>

  </div>

</section>

<!-- ===================== SEARCH SECTION ===================== -->

<section class="search-section">

  <div class="container">

    <div class="section-title">
      <h2>Find The Perfect Tutor</h2>
      <p>
        Search by subject, location, or educational level.
      </p>
    </div>

    <div class="search-card">
      <form action="/brilliance/view-tutors.php" method="get" style="display:contents;">

        <div class="search-field">
          <i class="fa-solid fa-book"></i>
          <input type="text" name="subject" placeholder="Subject" value="<?php echo htmlspecialchars($_GET['subject'] ?? ''); ?>">
        </div>

        <div class="search-field">
          <i class="fa-solid fa-location-dot"></i>
          <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
        </div>

        <div class="search-field">
          <i class="fa-solid fa-school"></i>
          <input type="text" name="level" placeholder="Class Level (optional)" value="<?php echo htmlspecialchars($_GET['level'] ?? ''); ?>">
        </div>

        <button type="submit" class="search-btn">
          <i class="fa-solid fa-magnifying-glass"></i>
          Search Tutors
        </button>

      </form>
    </div>

  </div>

</section>

<!-- ===================== TOP TUTORS ===================== -->

<section class="top-tutors">

  <div class="container">

    <div class="section-title">
      <h2>Top Rated Tutors</h2>
      <p>
        Meet some of our highest-rated educators.
      </p>
    </div>

    <div class="tutors-grid">
      <?php
      // dynamic top 3 tutors by rating
      require_once __DIR__ . '/classes/Database.php';
      try {
          $db = new Database(); $conn = $db->connect();
          $sql = "SELECT u.id, u.fullname, t.profile_pic, COALESCE(t.rating_avg,0) AS rating_avg, COALESCE(t.total_reviews,0) AS total_reviews, t.hourly_rate, t.location, t.bio FROM users u LEFT JOIN tutor_profile t ON u.id = t.user_id WHERE u.role = 'tutor' ORDER BY t.rating_avg DESC, t.total_reviews DESC LIMIT 3";
          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $top = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
          $top = [];
      }

      if (empty($top)) {
          echo '<p>No top tutors available yet.</p>';
      } else {
          foreach ($top as $t) {
              $img = !empty($t['profile_pic']) ? '/brilliance/uploads/' . htmlspecialchars($t['profile_pic']) : 'assets/images/logo.png';
              $rating = isset($t['rating_avg']) ? number_format((float)$t['rating_avg'],2) : '0.00';
              $reviews = isset($t['total_reviews']) ? (int)$t['total_reviews'] : 0;
              $bio = !empty($t['bio']) ? htmlspecialchars(substr($t['bio'],0,120)) : '';
              $name = htmlspecialchars($t['fullname']);
              $link = '/brilliance/tutor/view-tutor.php?id=' . (int)$t['id'];
              echo "<div class=\"tutor-card\">";
              echo "<img src=\"{$img}\" alt=\"Tutor\">";
              echo "<div class=\"rating\">⭐ {$rating} ({$reviews})</div>";
              echo "<h3>{$name}</h3>";
              echo "<span class=\"subject\">" . ($t['location'] ? htmlspecialchars($t['location']) : 'Tutor') . "</span>";
              echo "<p>{$bio}</p>";
              echo "<a href=\"{$link}\" class=\"btn-primary\">View Profile</a>";
              echo "</div>";
          }
      }
      ?>
    </div>

  </div>

</section>

<!-- ===================== WHY CHOOSE US ===================== -->

<section class="why-us">

  <div class="container">

    <div class="section-title">
      <h2>Why Parents Choose Brilliance</h2>

      <p>
        A safer and smarter way to find educational support.
      </p>
    </div>

    <div class="features-grid">

      <div class="feature-card">
        <i class="fa-solid fa-user-check"></i>
        <h3>Verified Tutors</h3>
        <p>
          Every tutor is screened and verified before approval.
        </p>
      </div>

      <div class="feature-card">
        <i class="fa-solid fa-shield-halved"></i>
        <h3>Safe Learning</h3>
        <p>
          Parents connect with trusted and qualified educators.
        </p>
      </div>

      <div class="feature-card">
        <i class="fa-solid fa-calendar-days"></i>
        <h3>Easy Scheduling</h3>
        <p>
          Organize lessons without unnecessary stress.
        </p>
      </div>

      <div class="feature-card">
        <i class="fa-solid fa-comments"></i>
        <h3>Direct Communication</h3>
        <p>
          Chat directly with tutors before booking.
        </p>
      </div>

      <div class="feature-card">
        <i class="fa-solid fa-star"></i>
        <h3>Reviews & Ratings</h3>
        <p>
          Make informed decisions using parent feedback.
        </p>
      </div>

      <div class="feature-card">
        <i class="fa-solid fa-chart-line"></i>
        <h3>Better Results</h3>
        <p>
          Personalized support for stronger academic performance.
        </p>
      </div>

    </div>

  </div>

</section>

<!-- ===================== CTA ===================== -->

<section class="cta">

  <div class="container">

    <div class="cta-box">

      <h2>
        Start Your Learning Journey Today
      </h2>

      <p>
        Join Brilliance and connect with trusted tutors
        who can help students achieve their goals.
      </p>

      <a href="auth/register.php" class="cta-btn">
        Join Brilliance
      </a>

    </div>

  </div>

</section>

<?php
include 'includes/footer.php';
?>



</body>
</html>