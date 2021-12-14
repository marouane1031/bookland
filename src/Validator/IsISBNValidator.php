<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsISBNValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\IsISBN */

        if (null === $value || '' === $value) {
            return;
        }

        if (preg_match('/^97[89][-\ ]([\d-]{13})/', $value) == 0) {
            return $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value . " ne respecte pas la forme d'un ISBN !")
                ->addViolation();
        }

        $numbers = explode("-", $value);
        if (count($numbers) != 5) {
            return $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value . " ne contient pas 4 groupes !")
                ->addViolation();
        }

        $sommePair = 0;
        $sommeImpair = 0;
        for ($i = 0; $i < count($numbers); $i++) {
            $num = $numbers[$i];
            $sum = 0;
            $rem = 0;
            for ($j = 0; $j <= strlen($num); $j++) {
                $rem = $num % 10;
                $sum = $sum + $rem;
                $num = $num / 10;
            }
            if ($i % 2 == 0) {
                $sommeImpair += $sum;
            } else $sommePair += $sum;
        }

        //dump(["pair" => $sommePair, "impair" => $sommeImpair]); die;

        if ((3 * $sommePair + $sommeImpair) % 10 != 0) {
            return $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', 
                $value . ", la somme (3X+Y) en position pair et impair n'est pas divisble par 10 !")
                ->addViolation();
        }
    }
}
