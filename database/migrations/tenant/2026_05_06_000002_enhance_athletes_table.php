<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            // Documentos e Contato do Atleta
            $table->string('document', 20)->nullable()->after('full_name'); // CPF
            $table->string('phone', 20)->nullable()->after('document');
            $table->text('address')->nullable()->after('phone');
            
            // Novos campos de Saúde e Físicos
            // (Note: height/weight já existem na tabela original, vamos apenas garantir subcategory)
            $table->string('subcategory', 20)->nullable()->after('birth_date'); // Ex: Sub-17
            
            // Dados do Responsável (Faturamento)
            $table->string('guardian_document', 20)->nullable()->after('guardian_email');
            
            // Termos e Verificação (Gamificação)
            $table->boolean('terms_accepted')->default(false)->after('insurance_info');
            $table->boolean('insurance_accepted')->default(false)->after('terms_accepted');
            $table->boolean('is_verified')->default(false)->after('insurance_accepted');
            $table->integer('profile_completion')->default(0)->after('is_verified');

            // Alterar position para JSON para suportar múltiplas posições
            // Como SQLite não suporta change() de string para json facilmente, 
            // no Postgres vamos usar uma abordagem segura.
            $table->json('positions')->nullable()->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn([
                'document', 'phone', 'address', 'subcategory', 
                'guardian_document', 'terms_accepted', 'insurance_accepted', 
                'is_verified', 'profile_completion', 'positions'
            ]);
        });
    }
};
