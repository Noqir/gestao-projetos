@php
    $raw = $exception?->getMessage();
    $message = $raw && $raw !== 'This action is unauthorized.' ? $raw : 'Você não tem permissão para acessar esta página.';
@endphp
@include('errors.layout', ['code' => 403, 'title' => 'Acesso negado', 'heading' => 'Acesso negado', 'message' => $message])
