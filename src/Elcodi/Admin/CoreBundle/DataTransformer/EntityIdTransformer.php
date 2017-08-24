<?php
namespace Elcodi\Admin\CoreBundle\DataTransformer;

use Doctrine\ORM\EntityManager;
// use Doctrine\Common\Persistence\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityIdTransformer implements DataTransformerInterface
{
    private $em;
    private $class;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    /**
     * Transforms an object (original) to a id
     * The transform() method is responsible for converting the data used in your code to a format that can be rendered in your form
     * @param  Issue|null $original
     * @return string
     */
    public function transform($original)
    {
        if ($original) {
            return $original->getId();
        }

    }

    /**
     * Transforms a id (number) to an object (issue).
     *
     * @param  string $submitted
     * @return Issue|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($submitted)
    {
        if ($submitted == null) {
            return null;
        }

        $employee = $this->em
            ->getRepository($this->class)
            ->find($submitted);

        return $employee;
    }
}
