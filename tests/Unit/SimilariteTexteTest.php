<?php

namespace Tests\Unit;

use App\Support\SimilariteTexte;
use PHPUnit\Framework\TestCase;

class SimilariteTexteTest extends TestCase
{
    public function test_identical_texts_have_maximum_similarity(): void
    {
        $text = 'Recherche operationnelle gestion des stocks optimisation';

        $this->assertEqualsWithDelta(1.0, SimilariteTexte::cosinus($text, $text), 0.000001);
    }

    public function test_different_texts_have_no_similarity(): void
    {
        $this->assertSame(0.0, SimilariteTexte::cosinus('biologie oceanographie', 'informatique programmation'));
    }
}
