<h3><?= $job->title ?></h3>
<p><?= $job->url ?></p>
<p><?= $job->snippet ?></p>
<p><?= $job->client->country ?></p>
<p><?= implode($job->skills) ?> </p>
<p><?= $job->budget ?></p>
<hr/>