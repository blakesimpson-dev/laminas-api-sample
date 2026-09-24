<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Profile;

use Doctrine\ORM\EntityRepository;

/** @extends EntityRepository<ProfileEntity> */
final class ProfileRepository extends EntityRepository {}
