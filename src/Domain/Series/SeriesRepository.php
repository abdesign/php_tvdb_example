<?php

namespace App\Domain\Series;

use Doctrine\ORM\EntityRepository;

class SeriesRepository extends EntityRepository implements SeriesRepositoryInterface
{
  /**
   * [findIds description]
   * @param  Array $tvdbIds [An array of TheTVDB ids to search on]
   * @return Array          [Array of Series ids]
   */
  public function findIds(Array $tvdbIds):Array
  {

    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->select('s.tvdbId');
    $qb->from('App\Domain\Series\Series', 's');
    $qb->add('where',$qb->expr()->in('s.tvdbId', ':tvdbIds'))->setParameter('tvdbIds',$tvdbIds);

    $query = $qb->getQuery();

    return $query->getResult();
  }

  /**
   * Returns an array of Series Names with the TheTBDB ids
   * @param  Array $tvdbIds [An array of TheTVDB ids to search on]
   * @return Array          [Array of names and TheTBDB ids]
   */
  public function findNames(Array $tvdbIds):Array
  {

    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->select('s.seriesName,s.tvdbId,s.id');
    $qb->from('App\Domain\Series\Series', 's');
    $qb->add('where',$qb->expr()->in('s.tvdbId', ':tvdbIds'))->setParameter('tvdbIds',$tvdbIds);

    $query = $qb->getQuery();

    return $query->getResult();
  }

  /**
   * Returns an array of basic information about a Series
   * @param  Array $tvdbIds [An array of TheTVDB ids to search on]
   * @return Array          [Array iincluding series name, tvdb id, overview, and thumbnail]
   */
  public function findSimple(Array $tvdbIds):Array
  {

    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->select('s.seriesName,s.tvdbId,s.id,s.overview,s.thumbnail');
    $qb->from('App\Domain\Series\Series', 's');
    $qb->add('where',$qb->expr()->in('s.tvdbId', ':tvdbIds'))->setParameter('tvdbIds',$tvdbIds);

    $query = $qb->getQuery();

    return $query->getResult();
  }
}
