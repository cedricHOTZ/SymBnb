<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($value): mixed
    {
        if ($value === null) {
            return '';
        }

        return $value->format('d/m/Y');
    }


    public function reverseTransform($frenchDate): mixed
    {
        //frenchDate = 21/09/2019
        if ($frenchDate === null) {
            //exeption
            throw new TransformationFailedException("Vous devez fournir une date");
        }

        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        if ($date === false) {
            //exeption
            throw new TransformationFailedException("Le format de date n'est pas bon");
        }

        return $date;
    }
}
