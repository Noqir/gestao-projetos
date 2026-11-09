<x-app-layout>
    <x-slot name="title">Meu perfil</x-slot>

    <div class="mx-auto max-w-3xl">
        <x-page-header title="Meu perfil" subtitle="Gerencie seus dados de acesso e a segurança da sua conta." />

        <div class="space-y-6">
            <x-card>@include('profile.partials.update-profile-information-form')</x-card>
            <x-card>@include('profile.partials.update-password-form')</x-card>
            <x-card class="border-rose-200 dark:border-rose-500/20">@include('profile.partials.delete-user-form')</x-card>
        </div>
    </div>
</x-app-layout>
