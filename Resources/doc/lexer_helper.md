Lexer Helper
============

This bundle provides a lexer helper that can decode a syntax allowing to power up your REST requests.

Introduction
------------

Let's explain the concept in a straightforward example.
Imagine you have a REST API upon a database resource House:

| Size  | Price  | Location
|-------|--------|----------
| 80    | 300000 | Paris
| 100   | 200000 | Madrid
| 100   | 350000 | New-York
| 150   | 500000 | Tokyo
| 250   | 700000 | Paris


The request `http://mybusiness.com/api/houses` [GET] allow you to retrieve some houses from given criteria.
For instance:

```bash
http://mybusiness.com/api/houses?size=100                 # retrieve the houses of size 100m²
http://mybusiness.com/api/houses?size=100&location=Paris  # retrieve the houses of size 100m² in Paris
```

Unfortunately, this is not very efficient to retrieve the houses between a min and max size and a min and max price. 
Of course, you can use that kind of pattern:

```bash
http://mybusiness.com/api/houses?min_size=100&max_size=200&min_price=200000&max_price=300000
```

This works for ranges, ok. But what if I do not want to live in Madrid?
Let's see how this bundle can help you to deal with that kind of things.

Syntax explanation
------------------

No long speeches, here how to represent the fact that I do not want to retrieve the houses in Madrid:

```bash
http://mybusiness.com/api/houses?location=!~~Madrid
```

I want to retrieve the houses from 100m² to 200m² and a price from 200000 to 300000:

```bash
http://mybusiness.com/api/houses?size=>~~100~-~<~~200&price=>~~200000~-~<~~300000
```

I just want the houses in Paris and Tokyo:

```bash
http://mybusiness.com/api/houses?location=in~~Paris~~Tokyo
# equivalent to:
http://mybusiness.com/api/houses?location=Paris~*~Tokyo
```

Implemented operators
---------------------

Here is the list of already implemented operator:

- `=` : equal to
- `!` : different of
- `<` : lower than
- `>` : greater than
- `<=` : lower than or equal to
- `>=` : greater than or equal to
- `in` : in the list

Implemented associations
------------------------

- `~-~` : and
- `~*~` : or

How to handle it in your code
-----------------------------

Of course, you have to handle this syntax in your code. 
As for now, a decorator is provided for some of the query builders of doctrine (ORM and MongoDB).
To use it, you just have to do 2 things:
	
- Extend one of the defined object repository with your own [custom repository](http://symfony.com/doc/current/book/doctrine.html#custom-repository-classes).
- Call the match method on the query builder.

For Doctrine ORM:

```php
namespace My\BusinessBundle\Document;

use Da\ApiServerBundle\Doctrine\ORM\ObjectRepository;

class HouseRepository extends ObjectRepository
{
    /**
     * Retrieve a set of matched houses from optional parameters.
     *
     * @param string $size
     * @param string $price
     * @param string $location
     *
     * @return array A set of matched houses.
     */
    public function retrieve(
        $size = null, 
        $price = null, 
        $location = null
    )
    {
        $qb = $this->createQueryBuilder();

        if (null !== $size) {
            $qb->match('size', $size);
        }
        if (null !== $price) {
            $qb->match('price', $price);
        }
        if (null !== $location) {
            $qb->match('location', $location);
        }

        return $qb->getQuery()->execute();
    }
}
```

For Doctrine MongoDB, use `use Da\ApiServerBundle\Doctrine\MongoDB\ObjectRepository;` instead of `use Da\ApiServerBundle\Doctrine\ORM\ObjectRepository;`.
The use of match is the same for the ORM and the MongoDB ODM.

And that's all! Now, the syntax should be decoded by your query builder.