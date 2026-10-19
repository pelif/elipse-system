<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'status' => 'Em análise',
                'type' => 1,
                'observacoes' => 'Estado inicial. Etapa da negociação comercial e coleta das informações preliminares do Job.'
            ],
            [
                'status' => 'Orçar',
                'type' => 1,
                'observacoes' => 'Nesta etapa, o esqueleto das Ordens de Serviço do Job já foi preenchido, e é necessário que o pessoal de produção faça a cotação com os fornecedores para apresentar os valores ao cliente.'
            ],
            [
                'status' => 'Faturar',
                'type' => 1,
                'observacoes' => 'O Job foi executado e entregue, portanto, deve ser encaminhado para o departamento financeiro realizar a emissão da NF e provisionar o pagamento dos terceiros. Não se deve cancelar um Job a partir deste estágio.'
            ],
            [
                'status' => 'Concluído',
                'type' => 1,
                'observacoes' => 'O Job foi executado e o cliente já pagou por ele. Passa para um estágio onde não se deve mais alterar informações.'
            ],
            [
                'status' => 'Cancelado',
                'type' => 1,
                'observacoes' => 'Por algum motivo, o cliente pediu o cancelamento do Job. Deve-se evitar o cancelamento para não acarretar no desperdício de esforço da equipe e possivelmente em perdas financeiras.'
            ],
            [
                'status' => 'Outros',
                'type' => 1,
                'observacoes' => 'Este estado não deve ser utilizado. Existe apenas para cadastrar passivo legado importado do sistema antigo.'
            ],
            [
                'status' => 'Não Agendado',
                'type' => 2,
                'observacoes' => 'Status inicial. Nenhum e-mail será enviado à produção enquanto a OS permanecer neste status..'
            ],
            [
                'status' => 'Provisório',
                'type' => 2,
                'observacoes' => 'As informações da OS foram preenchidas de modo preliminar, portanto, é enviado agenda para o estúdio com status "Provisório" de modo a provisionar a equipe, mas nenhuma atividade de produção deve ser executada.'
            ],
            [
                'status' => 'Agendado',
                'type' => 2,
                'observacoes' => 'A agenda foi confirmada pelo cliente e o pessoal de estúdio deve começar as atividades de produção, sendo a primeira delas coletar informações (JOTFORM) para preparar o checklist do Job (ASANA). '
            ],
            [
                'status' => 'Finalizado',
                'type' => 2,
                'observacoes' => 'Neste ponto, a Live já aconteceu e o relatório do evento já foi enviado para o solicitante (no caso de ao vivo) e a gravação já foi entregue. Deste modo, as ações de produção estão encerradas e a Ordem de Serviço pode ser dada como concluída.'
            ],
            [
                'status' => 'Cancelado',
                'type' => 2,
                'observacoes' => 'Se por qualquer motivo a OS foi cancelada, ela entra neste status, mas não é eliminada do registro, pois pode ser que já tenha sido realizado esforço de produção e é necessário buscar acordo para ressarcimento junto ao cliente.Se por qualquer motivo a OS foi cancelada, ela entra neste status, mas não é eliminada do registro, pois pode ser que já tenha sido realizado esforço de produção e é necessário buscar acordo para ressarcimento junto ao cliente.'
            ],
            [
                'status' => 'Outros',
                'type' => 2,
                'observacoes' => 'Este estado não deve ser utilizado. Existe apenas para cadastrar passivo legado importado do sistema antigo.'
            ],
        ];

        foreach ($statuses as $key => $status) {
            Status::firstOrCreate(
                [
                    'status' => $status['status'],
                    'type' => $status['type']
                ],
                [
                    'status' => $status['status'],
                    'type' => $status['type'],
                    'observacoes' => $status['observacoes']
                ]
            );
        }
    }
}
