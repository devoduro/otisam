<span class="badge text-bg-{{ ['active' => 'success', 'draft' => 'warning', 'retired' => 'secondary'][$status] ?? 'light' }}">{{ ucfirst($status) }}</span>
