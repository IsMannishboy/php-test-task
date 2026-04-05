<div>
 <div>
        <h2>Tickets</h2>
        <p>Total Tickets: {{ $ticketsCount }}</p>
        <p>filters</p>
        <ul>
            <li>
                <label for="customer-id-filter">Customer ID:</label>
                <input type="text" id="customer-id-filter" placeholder="Enter customer ID">
            </li>
            <li>
                <label for="status-filter">Status:</label>
                <select id="status-filter">
                    <option value="">All</option>
                    <option value="new">New</option>
                    <option value="processing">Processing</option>
                    <option value="done">Done</option>
                </select>
            </li>
            <li>
                <label for="date-filter">Date:</label>
                <input type="date" id="date-filter">
            </li>
            <li>
                <label for="email-filter">Email:</label>
                <input type="email" id="email-filter" placeholder="Enter customer email">
            </li>
            <li>
                <label for="phone-filter">Phone:</label>
                <input type="text" id="phone-filter" placeholder="Enter customer phone">
            </li>
            <button id="apply-filters">Apply Filters</button>
        </ul>
        <ul id="tickets-list">
            @foreach($tickets as $ticket)
                <ul>
                   <li>
                <span>customer id: </span>
                <span id="{{ $ticket->customer_id }}-customer_id">{{ $ticket->customer_id }}</span>
            </li>

                <li>
                <span>email: </span>
                <span id="{{ $ticket->id }}-email">
                    {{ $ticket->customer->email ?? 'no email' }}
                </span>
            </li>
            <li>
            <span>phone: </span>
                <span id="{{ $ticket->id }}-phone">
                    {{ $ticket->customer->phone ?? 'no phone' }}
                </span>
            </li>

            <li>
                <span>ticket id: </span>
                <span id="{{ $ticket->id }}-id">{{ $ticket->id }}</span>
            </li>

            <li>
                <span>topic: </span>
                <span id="{{ $ticket->id }}-topic">{{ $ticket->topic }}</span>
            </li>

            <li>
                <span>text: </span>
                <span id="{{ $ticket->id }}-text">{{ $ticket->text }}</span>
            </li>

            <li>
                <span>status: </span>
                <span id="{{ $ticket->id }}-status">{{ $ticket->status }}</span>
            </li>
            <li>
                <span>attachment: </span>

                @if($ticket->getFirstMedia('attachments'))
                    <a href="{{ $ticket->getFirstMedia('attachments')->getUrl() }}" target="_blank">
                        {{ $ticket->getFirstMedia('attachments')->file_name }}
                    </a>

                    <a href="/api/tickets/{{ $ticket->id }}/download">
                        Download
                    </a>
                @else
                    <span>no file</span>
                @endif
            </li>
                </ul>
                <button class="edit-button" data-id="{{ $ticket->id }}">Edit</button>
            @endforeach
        </ul>
        <div id="edit-zone" style="display: none;">
            <p>ticket id:<p id="editing-ticket-id"></p></p>
            <p  id="edit-topic-label" style="display: none;">   topic:         </p> <input type="text" id="edit-topic"  placeholder="Edit ticket topic" >
            <p  id="edit-status-label" style="display: none;">    status:        </p><input type="text" id="edit-status"  placeholder="Edit status" >
            <p id="edit-text-label"  style="display: none;">   text:          </p><input type="text" id="edit-text"  placeholder="Edit ticket text" >
        </div>
        <button id="save-button">Save</button>
    </div>
</div>

<script src="{{ asset('js/index.js') }}"></script>

</script>