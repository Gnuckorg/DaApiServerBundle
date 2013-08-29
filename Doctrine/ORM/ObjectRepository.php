<?php

namespace Da\ApiServerBundle\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;
use Da\ApiServerBundle\Model\ObjectRepositoryInterface;

/**
 * The class allowing to use the decorated query builder
 * for relational databases.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
class ObjectRepository extends EntityRepository implements ObjectRepositoryInterface
{
    /**
     * The class name of the decorators.
     *
     * @var array
     */
    private static $decorators;

    /**
     * Get the directory where the default decorators are.
     *
     * @return string The directory.
     */
    protected static function getDecoratorDirectory()
    {
        return __DIR__.'/Decorator';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDecorators()
    {
        if (null === static::$decorators)
        {
            $dir = static::getDecoratorDirectory();
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file !== "." && $file !== ".." && !is_dir($dir.'/'.$file)) {
                        // The decorator should declare it itself with the static 
                        // method addDecoratorClassName in its file. 
                        require_once($dir.'/'.$file);
                        $class = str_replace('/', '\\', $path);
                        $class = substr($class, strpos($class, '\Da\ApiServerBundle');
                        static::$decorators[] = $class;
                    }
                }
                closedir($handle);
            }
        }

        return static::$decorators;
    }

    /**
     * {@inheritdoc}
     */
    public static function addDecoratorClassName($decoratorClassName)
    {
        if (null === self::$decorators)
            self::$decorators = array();
        self::$decorators[] = $decoratorClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder($documentName = null)
    {
        $decorated = parent::createQueryBuilder($documentName);
        foreach (self::getDecorators() as $decoratorClassName)
        {
            $decorator = new $decoratorClassName($decorated);
            $decorated = $decorator;
        }
        
        return $decorated;
    }
}