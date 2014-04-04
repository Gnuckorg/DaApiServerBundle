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
     * The directory of the default decorators.
     *
     * @var string
     */
    private static $decoratorsDirectory;

    /**
     * The namespaces of the default decorators.
     *
     * @var string
     */
    private static $decoratorsNamespace;

    /**
     * Get the directory where the default decorators are.
     *
     * @return string The directory.
     */
    protected static function getDecoratorDirectory()
    {
        if (null === self::$decoratorsDirectory)
            return __DIR__.'/Decorator';
        return self::$decoratorsDirectory;
    }

    /**
     * Get the directory where the default decorators are.
     *
     * @return string The directory.
     */
    protected static function getDecoratorNamespace()
    {
        if (null === self::$decoratorsNamespace)
            return '\Da\ApiServerBundle\Doctrine\ORM\Decorator';
        return self::$decoratorsNamespace;
    }

    /**
     * Set the directory where the default decorators are.
     *
     * @param string $directory The directory.
     */
    public static function setDecoratorDirectory($directory, $namespace)
    {
        self::$decoratorsDirectory = $directory;
        self::$decoratorsNamespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDecorators()
    {
        if (null === self::$decorators)
        {
            $dir = self::getDecoratorDirectory();
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    $path = realpath($dir.'/'.$file);
                    if ($file !== "." && $file !== ".." && !is_dir($dir.'/'.$file)) {
                        // The decorator should declare it itself with the static 
                        // method addDecoratorClassName in its file. 
                        require_once($path);
                        $class = str_replace('/', '\\', $path);
                        $class = substr($class, strrpos($class, '\\'));
                        $class = substr($class, 0, strlen($class) - 4);
                        self::addDecoratorClassName(self::getDecoratorNamespace().$class);
                    }
                }
                closedir($handle);
            }
        }

        return self::$decorators;
    }

    /**
     * {@inheritdoc}
     */
    public static function addDecoratorClassName($decoratorClassName)
    {
        if (null === self::$decorators) {
            self::$decorators = array();
        }
        self::$decorators[] = $decoratorClassName;
    }

    /**
     * Return the native query builder.
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return Doctrine\MongoDB\Query\Builder The native query builder.
     */
    public function getNativeQueryBuilder($alias, $indexBy = null)
    {
        return parent::createQueryBuilder($alias, $indexBy);
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder($alias, $indexBy = null)
    {
        $decorated = $this->getNativeQueryBuilder($alias, $indexBy);
        
        foreach (self::getDecorators() as $decoratorClassName) {
            $decorator = new $decoratorClassName($decorated);
            $decorated = $decorator;
        }

        foreach($this->getClassMetaData()->fieldMappings as $fieldName => $fieldMapping) {
            $decorated->registerFieldType($fieldName, $fieldMapping['type']);
        }

        return $decorated;
    }
}