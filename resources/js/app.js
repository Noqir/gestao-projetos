import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/**
 * Exibe um aviso (toast). Usado pelas respostas JSON do Kanban;
 * as mensagens de sessão (redirect) são exibidas pelo componente x-flash.
 */
window.notify = (message, type = 'success') => {
    window.dispatchEvent(new CustomEvent('notify', { detail: { message, type } }));
};

document.addEventListener('alpine:init', () => {
    /* Tema claro/escuro persistido no navegador */
    Alpine.data('themeToggle', () => ({
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            try {
                localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            } catch (e) {
                /* navegação privada: apenas não persiste */
            }
        },
    }));

    /* Pilha de avisos no canto da tela */
    Alpine.data('toaster', (initial = []) => ({
        toasts: [],
        nextId: 1,
        init() {
            initial.forEach((t) => this.push(t));
            window.addEventListener('notify', (e) => this.push(e.detail));
        },
        push({ message, type = 'success' }) {
            const id = this.nextId++;
            this.toasts.push({ id, message, type, visible: true });
            setTimeout(() => this.dismiss(id), 4500);
        },
        dismiss(id) {
            const toast = this.toasts.find((t) => t.id === id);
            if (toast) toast.visible = false;
            setTimeout(() => (this.toasts = this.toasts.filter((t) => t.id !== id)), 300);
        },
    }));

    /*
     * Quadro Kanban: arrastar e soltar com SortableJS.
     * Ao soltar um cartão, envia PATCH /tasks/{id}/status com o novo status e
     * a posição; se o servidor recusar, o cartão volta para o lugar original.
     */
    Alpine.data('kanban', (initialCounts = {}, canMove = true) => ({
        counts: initialCounts,
        saving: false,
        init() {
            if (!canMove) return;

            this.$el.querySelectorAll('[data-column]').forEach((list) => {
                Sortable.create(list, {
                    group: 'kanban',
                    animation: 180,
                    easing: 'cubic-bezier(0.2, 0, 0, 1)',
                    draggable: '[data-task]',
                    ghostClass: 'kanban-ghost',
                    dragClass: 'kanban-drag',
                    delayOnTouchOnly: true,
                    delay: 120,
                    onEnd: (evt) => this.move(evt),
                });
            });
        },
        recount() {
            this.$el.querySelectorAll('[data-column]').forEach((list) => {
                this.counts[list.dataset.column] = list.querySelectorAll('[data-task]').length;
            });
        },
        async move(evt) {
            if (evt.from === evt.to && evt.oldIndex === evt.newIndex) return;

            const card = evt.item;
            const status = evt.to.dataset.column;
            const position = [...evt.to.querySelectorAll('[data-task]')].indexOf(card);
            this.recount();
            this.saving = true;

            try {
                const response = await fetch(card.dataset.statusUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ status, position }),
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'Não foi possível mover a tarefa.');

                card.dataset.status = status;
                window.notify(data.message || 'Tarefa atualizada.');
            } catch (error) {
                // Desfaz o movimento na tela para refletir o estado do banco
                card.remove();
                const reference = evt.from.querySelectorAll('[data-task]')[evt.oldIndex] ?? null;
                evt.from.insertBefore(card, reference);
                this.recount();
                window.notify(error.message, 'error');
            } finally {
                this.saving = false;
            }
        },
    }));
});

window.Alpine = Alpine;
Alpine.start();
