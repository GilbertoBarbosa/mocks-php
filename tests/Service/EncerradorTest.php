<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Encerrador;

class EncerradorTest extends TestCase
{
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $fiat147 = new Leilao(
            'Fiat 147 0Km',
            new \DateTimeImmutable('8 days ago')
        );
        
        $variant = new Leilao(
            'Variant 1972 0Km',
            new \DateTimeImmutable('10 days ago')
        );

        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$fiat147, $variant]);
        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$fiat147, $variant]);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$fiat147],
                [$variant]
            );

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        $leiloes = [$fiat147, $variant];
        self::assertCount(2, $leiloes);
        self::assertTrue($leiloes[0]->estaFinalizado());
        self::assertTrue($leiloes[1]->estaFinalizado());
    }
}