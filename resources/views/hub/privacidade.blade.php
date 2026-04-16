@extends('layouts.hub')

@section('title', 'Privacidade — Clinky.cc')
@section('description', 'Política de privacidade do Clinky.cc. Não colectamos dados pessoais.')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12">

    <a href="{{ route('home') }}" class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
        ← Clinky.cc
    </a>

    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 mt-6 mb-8">
        Privacidade
    </h1>

    <div class="prose prose-zinc dark:prose-invert prose-sm max-w-none space-y-6">

        <section>
            <h2 class="text-lg font-semibold">Quem somos</h2>
            <p>Clinky.cc é um projecto de entretenimento que disponibiliza mini-sites virais, gratuitos e anónimos.</p>
        </section>

        <section>
            <h2 class="text-lg font-semibold">Dados que colectamos</h2>
            <p>Nenhum dado pessoal. Usamos apenas analytics agregado e anónimo via Fathom Analytics, que não utiliza cookies e é GDPR-native.</p>
        </section>

        <section>
            <h2 class="text-lg font-semibold">Cookies</h2>
            <p>Usamos apenas o cookie de sessão técnico do Laravel, necessário para protecção CSRF. Não usamos cookies de tracking ou publicidade.</p>
        </section>

        <section>
            <h2 class="text-lg font-semibold">Inputs de texto</h2>
            <p>Alguns mini-sites pedem texto (ex: nome, situação). Este texto é processado em memória e nunca é guardado em base de dados ou ficheiros de log.</p>
        </section>

        <section>
            <h2 class="text-lg font-semibold">IA (Claude API)</h2>
            <p>Alguns mini-sites usam a Claude API da Anthropic para gerar conteúdo. Os inputs são enviados com a flag de não-treino activada, o que significa que a Anthropic não usa esses dados para treinar os seus modelos.</p>
        </section>

        <section>
            <h2 class="text-lg font-semibold">Cookies e Analytics</h2>
            <p>Usamos <strong>Google Analytics 4</strong> para perceber quais mini-sites funcionam melhor. O GA só carrega após aceitares no banner.</p>
            <p class="mt-2">Recolhemos: páginas visitadas, tempo na página, país aproximado, tipo de dispositivo.</p>
            <p class="mt-2"><strong>Não recolhemos:</strong> nome, email, endereço IP completo (anonimizado), nem qualquer input que escrevas nos mini-sites.</p>
            <p class="mt-2">Para revogar o consentimento, limpa os dados deste site no teu browser (o banner aparecerá novamente).</p>
        </section>

        <section>
            <h2 class="text-lg font-semibold">Os teus direitos</h2>
            <p>Como não guardamos dados pessoais, não existe informação tua para aceder, corrigir ou eliminar. Se tiveres alguma questão, contacta-nos.</p>
        </section>

    </div>

</div>
@endsection
