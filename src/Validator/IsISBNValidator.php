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

        $chaine = "";

        foreach ($numbers as $v){
            $chaine .= $v;
        }

        $NombrePair = 0;
        $NombreImpair = 0;
        for ($i = 0; $i < 13; $i++) {
            if( $i % 2 == 0){
                $NombrePair += $chaine[$i]; 
            }else{
                $NombreImpair += 3 * $chaine[$i];
            }
        }
        
        $Somme = $NombrePair + $NombreImpair;

        if($Somme % 10 != 0){
        return $this->context->buildViolation($constraint->message)
        ->setParameter('{{ value }}', 
        $value . ", la somme (3X+Y) en position pair et impair n'est pas divisble par 10 !")
        ->addViolation();
       }
    }
}
