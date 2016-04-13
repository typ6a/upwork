<?php foreach ($jobs as $job) : ?>
    <h1><?= $job->title ?></h1>
    <p><?= $job->description ?></p>
    <p><?= $job->url ?></p>
<?php endforeach; ?>