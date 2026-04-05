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
<script>
let applyFilters = document.getElementById("apply-filters");
applyFilters.onclick = async ()=>{
    const customerId = document.getElementById("customer-id-filter").value;
    const status = document.getElementById("status-filter").value;
    const date = document.getElementById("date-filter").value;
    const email = document.getElementById("email-filter").value;
    const phone = document.getElementById("phone-filter").value;
    console.log(date)
    const response = await fetch(`/tickets/filters`,{
        method:"GET",
        headers :{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "customerId": customerId,
            "status": status,
            "X-Date": date,
            "email": email,
            "phone": phone
        }
    });
    if(!response.ok){
        console.log(response)
        return
    }
    const tickets = await response.json();
    function createRow(labelText, valueText, id) {
                const li = document.createElement("li");

                const label = document.createElement("span");
                label.textContent = labelText + " ";

                const value = document.createElement("span");
                value.textContent = valueText;
                value.setAttribute("id", id);

                li.appendChild(label);
                li.appendChild(value);

                return li;
            }
        const ticketsList = document.getElementById("tickets-list");
        ticketsList.innerHTML = "";
        tickets.forEach(ticket => {
            ticketsList.appendChild(
                createRow("ticket id:", ticket.id, `${ticket.id}-id`)
            );

            ticketsList.appendChild(
                createRow("topic:", ticket.topic, `${ticket.id}-topic`)
            );

            ticketsList.appendChild(
                createRow("text:", ticket.text, `${ticket.id}-text`)
            );
            ticketsList.appendChild(
                createRow("status:", ticket.status, `${ticket.id}-status`)
            );
            ticketsList.appendChild(
                createRow("email:", ticket.customer?.email ?? "no email", `${ticket.id}-email`)
            );
            ticketsList.appendChild(
                createRow("phone:", ticket.customer?.phone ?? "no phone", `${ticket.id}-phone`)
            );
            ticketsList.appendChild(
                createRow("customer id:", ticket.customer_id, `${ticket.customer_id}-customer_id`)
            );
            const attachmentLi = document.createElement("li");

            const label = document.createElement("li");
            label.textContent = "attachment: ";

            const link = document.createElement("a");
            link.href = ticket.attachment_url; 
            link.textContent = ticket.attachment;

            if(ticket.attachment) {
                     const downloadBtn = document.createElement("a");
                        downloadBtn.href = `http://0.0.0.0:8000/tickets/${ticket.id}/download`;
                        downloadBtn.textContent = "Download";
                        attachmentLi.appendChild(downloadBtn);
            }

            attachmentLi.appendChild(label);
            attachmentLi.appendChild(link);

            ticketsList.appendChild(attachmentLi);
            const editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.classList.add("edit-button");
            editButton.setAttribute("data-id", ticket.id);

            ticketsList.appendChild(editButton);
        });

}

    const EditZone = document.getElementById("edit-zone");
const save = document.getElementById("save-button")
function GetEditingValues(){
        const topic = document.getElementById(`edit-topic`).value;
        const status = document.getElementById(`edit-status`).value;
        const text = document.getElementById(`edit-text`).value;
        return {topic:topic,status:status,text:text}
}
function GetOriginValues(ticketId){
        const topic = document.getElementById(`${ticketId}-topic`).textContent;
        const status = document.getElementById(`${ticketId}-status`).textContent;
        const text = document.getElementById(`${ticketId}-text`).textContent;
        console.log("origin values:",topic,status,text)
        return {topic:topic,status:status,text:text}
}
save.onclick = async ()=>{
    const ticketId = document.getElementById("editing-ticket-id").textContent
    const ChangedValues = DefineTopicChanges(GetEditingValues(),GetOriginValues(ticketId))
    if(ChangedValues == null){
        return
    }
    const response = await fetch(`/tickets/${ticketId}`,{
        method:"PUT",
            credentials: "include",
        headers:{
            "Content-Type":"application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body:JSON.stringify(            ChangedValues
)
    })
    if(response.ok){
        alert("Changes saved successfully!")
        const resp = await response.json();
        for(changes in resp.changes){
            document.getElementById(`${ticketId}-${changes}`).textContent = resp.changes[changes]
        }
        EditZone.style.display = "none";

    }else{
        alert(response.statusText," wrong data");
    }
}
function DefineTopicChanges(ChangedValues,OriginalValues){
    const changed = {}
    let NumberOfChanges = 0
    for(const key in OriginalValues){
        console.log("Origin:",OriginalValues[key]," - ",ChangedValues[key])
        if(OriginalValues[key]!= ChangedValues[key]){
            NumberOfChanges++
            changed[key] = ChangedValues[key]
        }
    }
    if(NumberOfChanges == 0){
            alert("No changes detected")
            return null
    }else{
            console.log("Changes detected:", changed)
            return changed

    }
}
document.getElementById("tickets-list").addEventListener("click", (e) => {
    if (e.target.classList.contains("edit-button")) {
        const ticketId = e.target.getAttribute("data-id");

        console.log("ticket id:", ticketId);

        const values = GetOriginValues(ticketId);

        EditZone.style.display = "block";
        document.getElementById("editing-ticket-id").innerHTML = ticketId;

        document.getElementById("edit-topic").value = values.topic;
        document.getElementById("edit-topic-label").style.display = "block";

        document.getElementById("edit-status").value = values.status;
        document.getElementById("edit-status-label").style.display = "block";

        document.getElementById("edit-text").value = values.text;
        document.getElementById("edit-text-label").style.display = "block";
    }
});
</script>