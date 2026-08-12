<!-- =====================================================
     6. PROJECTS — featured portfolio (DB-driven with static fallback)
     ===================================================== -->
<?php
$contactUrl  = $site['contact_url'] ?? 'contact.php';
$projectsUrl = $site['projects_url'] ?? 'projects.php';

$staticProjects = [
    ['no' => '01 &middot; Luxury Residence', 'title' => 'Villa Seraph', 'text' => '12,000 sq.ft. private residence in Mumbai — crafted around light, view and quiet luxury.',
     'img' => 'images/projects/theme-home-daylight-warm.webp', 'srcset' => '', 'alt' => 'Villa Seraph luxury residence exterior'],
    ['no' => '02 &middot; Architecture', 'title' => 'The Glasshouse', 'text' => 'A contemporary family home in Pune where floor-to-ceiling glazing dissolves the line between inside and garden.',
     'img' => 'images/glass_house@1000w.webp', 'srcset' => 'images/glass_house@1000w.webp 1000w, images/glass_house@2000w.webp 2000w', 'alt' => 'The Glasshouse contemporary home'],
    ['no' => '03 &middot; Hotel', 'title' => 'Hotel Aurelia', 'text' => 'A 65-key boutique hotel in Jaipur — sculpted lobby, guest suites and dining hall finished in heritage stone, warm wood and layered hospitality lighting.',
     'img' => 'images/hotel_aurelia@1000w.webp', 'srcset' => 'images/hotel_aurelia@1000w.webp 1000w, images/hotel_aurelia@2000w.webp 2000w', 'alt' => 'Hotel Aurelia lobby interior'],
    ['no' => '04 &middot; Interior Design', 'title' => 'Penthouse Noir', 'text' => 'A skyline penthouse in Bengaluru finished in stone, smoked oak and soft ambient light.',
     'img' => 'images/projects/theme-penthouse-light-camel.webp', 'srcset' => '', 'alt' => 'Penthouse Noir penthouse living space'],
    ['no' => '05 &middot; Renovation', 'title' => 'Garden Pavilion', 'text' => 'Heritage home restoration in Delhi — original details preserved, modern systems quietly added.',
     'img' => 'images/garden@1000w.webp', 'srcset' => 'images/garden@1000w.webp 1000w, images/garden@2000w.webp 2000w', 'alt' => 'Garden Pavilion renovation interior'],
    ['no' => '06 &middot; Commercial', 'title' => 'Skyline Offices', 'text' => 'Corporate headquarters in Noida designed for collaboration, calm and uncompromising build quality.',
     'img' => 'images/projects/theme-offices-silver-blue.webp', 'srcset' => '', 'alt' => 'Skyline Offices commercial workspace'],
];

$projects = $staticProjects;

try {
    if (class_exists('Project')) {
        $dbProjects = Project::publicList();
        if (count($dbProjects) >= 2) {
            $projects = [];
            $statusLabels = ['in_progress' => 'Ongoing', 'completed' => 'Completed'];
            foreach (array_slice($dbProjects, 0, 6) as $i => $p) {
                $thumb = (!empty($p['thumbnail']) && is_file(ROOT_PATH . '/' . $p['thumbnail']))
                    ? $p['thumbnail']
                    : 'images/projects/theme-home-daylight-warm.webp';
                $category = $p['category'] ?? ($statusLabels[$p['status']] ?? 'Project');
                $desc = trim((string)($p['description'] ?? ''));
                if ($desc === '') {
                    $desc = 'A Seraph Build Construction project crafted with premium materials and meticulous execution.';
                } elseif (mb_strlen($desc) > 160) {
                    $desc = mb_substr($desc, 0, 157) . '...';
                }
                $projects[] = [
                    'no'     => sprintf('%02d &middot; %s', $i + 1, e($category)),
                    'title'  => $p['name'],
                    'text'   => $desc,
                    'img'    => $thumb,
                    'srcset' => '',
                    'alt'    => $p['name'] . ' project',
                ];
            }
        }
    }
} catch (Throwable $e) {
    error_log('Homepage projects section: ' . $e->getMessage());
}
?>
<section id="projects" class="projects-section">
  <div class="container projects-section__head">
    <span class="eyebrow">Portfolio</span>
    <h2 class="projects-section__heading">Featured Projects</h2>
    <p class="projects-section__intro">A curated selection of residences, commercial spaces, and renovations crafted by our studio.</p>
  </div>

  <div class="projects-story" id="projectsStory">
    <div class="projects-story__inner">
      <div class="projects-bg" aria-hidden="true"></div>
      <div class="projects-story__text">
        <?php foreach ($projects as $i => $project): ?>
          <div class="projects-story__step">
            <div class="no"><?php echo $project['no']; ?></div>
            <h2><?php echo e($project['title']); ?></h2>
            <p><?php echo e($project['text']); ?></p>
            <?php if ($i === count($projects) - 1): ?>
              <a href="<?php echo e($projectsUrl); ?>" class="btn">View All Projects</a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="projects-story__visual">
        <?php foreach ($projects as $i => $project): ?>
          <img
            src="<?php echo e($project['img']); ?>"
            <?php if ($project['srcset']): ?>srcset="<?php echo e($project['srcset']); ?>" sizes="(min-width: 1400px) 1000px, 90vw"<?php endif; ?>
            alt="<?php echo e($project['alt']); ?>"
            width="1000" height="800"<?php echo $i === 0 ? ' class="active"' : ' loading="lazy"'; ?>>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="projects-story__rail" aria-hidden="true"><i id="projectsRail"></i></div>
    <div class="projects-story__dots" id="projectsDots">
      <?php foreach ($projects as $i => $project): ?>
        <button<?php echo $i === 0 ? ' class="active"' : ''; ?> aria-label="Project <?php echo $i + 1; ?>"></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
