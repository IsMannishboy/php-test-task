const URL = "http://localhost:8000";
let applyFilters = document.getElementById("apply-filters");
applyFilters.onclick = async ()=>{
   const customerId = document.getElementById("customer-id-filter").value;
    const status = document.getElementById("status-filter").value;
    const date = document.getElementById("date-filter").value;
    const email = document.getElementById("email-filter").value;
    const phone = document.getElementById("phone-filter").value;

    const params = new URLSearchParams();

    if (customerId) params.append('customerId', customerId);
    if (status) params.append('status', status);
    if (date) params.append('date', date);
    if (email) params.append('email', email);
    if (phone) params.append('phone', phone);

    const response = await fetch(`/tickets/filters?${params.toString()}`, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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
            console.log(ticket.attachment_url)
            console.log(ticket.attachment)
            console.log(ticket)
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

            if(ticket.attachment_url !== "#") {
                     const downloadBtn = document.createElement("a");
                        downloadBtn.href = `${URL}/api/tickets/${ticket.id}/download`;
                        downloadBtn.textContent = "Download";
                        attachmentLi.appendChild(downloadBtn);
                                    ticketsList.appendChild(attachmentLi);

            }

            attachmentLi.appendChild(label);
            attachmentLi.appendChild(link);

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
    const response = await fetch(`/api/tickets/${ticketId}`,{
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