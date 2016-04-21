<h1><?= $job->title ?></h1>
<p><?= $job->url ?></p>
<p><?= $job->snippet ?></p>
<p><?= $job->client->country ?></p>
<p><?= implode($job->skills) ?> </p>
<hr/>