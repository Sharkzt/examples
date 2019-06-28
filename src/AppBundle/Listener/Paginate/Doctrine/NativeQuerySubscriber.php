<?php


namespace AppBundle\Listener\Paginate\Doctrine;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NativeQuerySubscriber implements EventSubscriberInterface
{

    const MAIN_TABLE_ALIAS_REGEXP = '/FROM\s+(?P<table>[\w\d_]+)\s+(AS\s+)?(?P<alias>[\w\d_]+)/';
    const LIMIT_REGEXP = '/(LIMIT\s+\S+\s*((,\s*\S+)|(\s+OFFSET\s+\S))\s*)/';

    public function items(ItemsEvent $event)
    {
        if ($event->target instanceof NativeQuery) {
            $sql = $event->target->getSQL();

            if (!preg_match(self::MAIN_TABLE_ALIAS_REGEXP, $sql, $m)) {
                throw new \InvalidArgumentException('Can\'t determine the main table alias');
            }

            $parameters = $event->target->getParameters();

            $event->count = (int) (clone $event->target)
                ->setResultSetMapping((new Query\ResultSetMapping())->addScalarResult('c', 'c', Type::INTEGER))
                ->setSQL('SELECT COUNT(*) AS c FROM (' . preg_replace(self::LIMIT_REGEXP, '', $sql) . ') AS s')
                ->execute($parameters, Query::HYDRATE_SINGLE_SCALAR)
            ;
            $event->items = [];

            $parameters->add(new Query\Parameter(':limit', $event->getLimit(), Type::INTEGER));
            $parameters->add(new Query\Parameter(':offset', $event->getOffset(), Type::INTEGER));

            $event->items = $event->target->execute($parameters);

            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('items', 10 /*make sure to transform before any further modifications*/)
        );
    }


}