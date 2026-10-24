@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Comment Banks</div>
    <div class="settings-panel__sub">
        Reusable teacher comments by category and performance band. Teachers pick from these when writing report card
        feedback.
    </div>

    @if ($errors->any())
        <div style="background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    @php
        $categoryLabels = [
            'strength' => ['Strength', 'var(--pts-green-dark)', 'var(--pts-green-light)'],
            'weakness' => ['Weakness', '#DC2626', '#FEF2F2'],
            'general' => ['General', '#374151', '#F3F4F6'],
            'effort' => ['Effort', '#D97706', '#FFFBEB'],
        ];

        $bandLabels = [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'average' => 'Average',
            'poor' => 'Needs support',
        ];
    @endphp

    {{-- Add new comment --}}
    <div style="padding: 20px; background: var(--pts-bg); border-radius: 12px; margin-bottom: 26px;">
        <div style="font-size: 13px; font-weight: 800; color: #111827; margin-bottom: 14px;">
            <i class="fa-solid fa-plus" style="color: var(--pts-green-dark);"></i> Add a comment
        </div>

        <form method="POST" action="{{ route('settings.comment-banks.store') }}">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                        Category
                    </label>
                    <select name="category" required
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                       border-radius:9px; font-size:13px; font-family:inherit; outline:none; background:#fff;">
                        <option value="strength">Strength</option>
                        <option value="weakness">Weakness</option>
                        <option value="general">General</option>
                        <option value="effort">Effort</option>
                    </select>
                </div>

                <div>
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                        Band
                    </label>
                    <select name="band"
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                       border-radius:9px; font-size:13px; font-family:inherit; outline:none; background:#fff;">
                        <option value="">Any band</option>
                        <option value="excellent">Excellent</option>
                        <option value="good">Good</option>
                        <option value="average">Average</option>
                        <option value="poor">Needs support</option>
                    </select>
                </div>

                <div>
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                        Subject (optional)
                    </label>
                    <input type="text" name="subject" placeholder="e.g. Mathematics"
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                      border-radius:9px; font-size:13px; font-family:inherit; outline:none; background:#fff;">
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <label
                    style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                    Comment text
                </label>
                <textarea name="text" required minlength="10" maxlength="500" rows="3"
                    placeholder="e.g. Demonstrates strong problem-solving skills and consistently applies concepts to new situations."
                    style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                                     border-radius:9px; font-size:13px; font-family:inherit; outline:none;
                                     resize:vertical; background:#fff;"></textarea>
            </div>

            <button type="submit" style="padding: 10px 20px; background: var(--pts-black); color: #fff; border: none;
                               border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Add Comment
            </button>
        </form>
    </div>

    {{-- Existing comments by category --}}
    @if ($comments->isEmpty())
        <div style="padding: 40px 20px; text-align: center; background: var(--pts-bg); border-radius: 12px;">
            <i class="fa-solid fa-comment-dots" style="font-size: 28px; color: var(--pts-grey-light);
                                                              display: block; margin-bottom: 10px;"></i>
            <div style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                No comments in the bank yet
            </div>
            <div style="font-size: 12.5px; color: var(--pts-grey);">
                Add your first comment above. Teachers will be able to pick from these when writing report cards.
            </div>
        </div>
    @else
        @foreach ($grouped as $category => $items)
            @php $meta = $categoryLabels[$category] ?? $categoryLabels['general']; @endphp
            <div style="margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;
                                        padding-bottom: 8px; border-bottom: 1px solid var(--pts-grey-light);">
                    <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
                                             padding: 3px 10px; border-radius: 20px;
                                             background: {{ $meta[2] }}; color: {{ $meta[1] }};">
                        {{ $meta[0] }}
                    </span>
                    <span style="font-size: 12.5px; color: var(--pts-grey);">
                        {{ $items->count() }} comment{{ $items->count() === 1 ? '' : 's' }}
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach ($items as $comment)
                        <div style="border: 1px solid var(--pts-grey-light); border-radius: 10px; padding: 14px 18px;
                                                    background: #fff; display: flex; align-items: flex-start; gap: 14px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px;">
                                    @if ($comment->band)
                                        <span style="font-size: 10px; font-weight: 700; padding: 2px 8px;
                                                                         border-radius: 4px; background: var(--pts-bg); color: #374151;
                                                                         text-transform: uppercase; letter-spacing: 0.05em;">
                                            {{ $bandLabels[$comment->band] ?? $comment->band }}
                                        </span>
                                    @endif
                                    @if ($comment->subject)
                                        <span style="font-size: 10px; font-weight: 700; padding: 2px 8px;
                                                                         border-radius: 4px; background: #DBEAFE; color: #1E40AF;">
                                            {{ $comment->subject }}
                                        </span>
                                    @endif
                                    @if (!$comment->is_active)
                                        <span style="font-size: 10px; font-weight: 700; padding: 2px 8px;
                                                                         border-radius: 4px; background: #FEF2F2; color: #991B1B;">
                                            INACTIVE
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size: 13px; color: #374151; line-height: 1.6;">
                                    {{ $comment->text }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('settings.comment-banks.destroy', $comment->id) }}"
                                onsubmit="return confirm('Delete this comment?');" style="flex-shrink: 0;">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #DC2626; cursor: pointer;
                                                               font-size: 12px; padding: 4px 8px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

@endsection