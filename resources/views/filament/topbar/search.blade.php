<div
    class="lb-topbar-search-ctn"
    x-data="{
        focused: false,
        searchQuery: '',
        handleKey(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                $refs.searchInput.focus();
            }
        }
    }"
    @keydown.window="handleKey($event)"
>
    <div
        class="lb-topbar-search-box"
        :class="{ 'lb-search-focused': focused }"
    >
        <svg class="lb-search-icon" width="15" height="15" viewBox="0 0 20 20" fill="currentColor" style="width: 15px; height: 15px; min-width: 15px; min-height: 15px; max-width: 15px; max-height: 15px; display: block;">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
        </svg>

        <input
            x-ref="searchInput"
            type="text"
            class="lb-search-input"
            placeholder="Search courses, learners, batches..."
            x-model="searchQuery"
            @focus="focused = true"
            @blur="focused = false"
            @keydown.escape="$refs.searchInput.blur()"
        />

        <div class="lb-search-kbd-wrap" @click="$refs.searchInput.focus()">
            <kbd class="lb-search-kbd">Ctrl + K</kbd>
        </div>
    </div>
</div>

<style>
    .lb-topbar-search-ctn {
        display: inline-flex;
        align-items: center;
        margin-right: 0.5rem;
    }

    .lb-topbar-search-box {
        display: flex;
        align-items: center;
        background-color: #F7F7F5;
        border: 1px solid #E5E5E5;
        border-radius: 9999px;
        padding: 0 0.75rem 0 0.875rem;
        height: 2.25rem; /* 36px */
        width: 280px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        box-sizing: border-box;
    }

    .lb-topbar-search-box:hover {
        background-color: #FFFFFF;
        border-color: #D1D1D1;
    }

    .lb-topbar-search-box.lb-search-focused {
        background-color: #FFFFFF;
        border-color: #FF5F00;
        box-shadow: 0 0 0 3px rgba(255, 95, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.05);
        width: 320px;
    }

    .lb-search-icon {
        width: 15px;
        height: 15px;
        color: #6B6B6B;
        flex-shrink: 0;
        margin-right: 0.5rem;
        transition: color 0.2s ease;
    }

    .lb-search-focused .lb-search-icon {
        color: #FF5F00;
    }

    .lb-search-input {
        background: transparent;
        border: none;
        outline: none;
        font-size: 0.75rem;
        color: #0A0A0A;
        width: 100%;
        padding: 0;
        margin: 0;
        line-height: 1;
        font-family: inherit;
    }

    .lb-search-input::placeholder {
        color: #9CA3AF;
        font-weight: 400;
    }

    .lb-search-kbd-wrap {
        display: flex;
        align-items: center;
        margin-left: 0.5rem;
        flex-shrink: 0;
        cursor: pointer;
    }

    .lb-search-kbd {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.625rem; /* 10px */
        font-weight: 600;
        color: #6B6B6B;
        background: #FFFFFF;
        border: 1px solid #E5E5E5;
        border-radius: 6px;
        padding: 0.15rem 0.375rem;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        line-height: 1;
        white-space: nowrap;
    }

    .lb-search-focused .lb-search-kbd {
        border-color: rgba(255, 95, 0, 0.3);
        color: #FF5F00;
    }

    /* Small screen adjustments */
    @media (max-width: 767px) {
        .lb-topbar-search-box {
            width: 180px;
        }

        .lb-topbar-search-box.lb-search-focused {
            width: 220px;
        }

        .lb-search-kbd-wrap {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .lb-topbar-search-ctn {
            display: none;
        }
    }
</style>
