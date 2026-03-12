
// supabase.js
import { createClient } from "https://esm.sh/@supabase/supabase-js@2";

const supabaseClient = createClient(
    "https://plxoonwsguadkqisevxh.supabase.co",
    "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InBseG9vbndzZ3VhZGtxaXNldnhoIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzE5ODUyNzUsImV4cCI6MjA4NzU2MTI3NX0.RTZp4Bp9YrZWkklaGOU71NpeJuAaNSfklqpqgi9KWCU"
);

export default supabaseClient;
