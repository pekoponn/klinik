<?php
declare(strict_types=1);

namespace App\Command;

use App\Utility\DataCipher;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Datasource\ConnectionManager;

class EncryptPatientCodesCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $conn = ConnectionManager::get('default');
        $rows = $conn->execute(
            'SELECT id, code FROM patients WHERE code_hash IS NULL'
        )->fetchAll('assoc');

        $io->out('Baris yang akan dienkripsi: ' . count($rows));

        $conn->transactional(function ($c) use ($rows) {
            foreach ($rows as $r) {
                $plain = DataCipher::decrypt($r['code']);
                $c->update('patients', [
                    'code' => DataCipher::encrypt($plain),
                    'code_hash' => DataCipher::hash($plain),
                ], ['id' => $r['id']]);
            }
        });

        $io->success('Selesai.');

        return static::CODE_SUCCESS;
    }
}