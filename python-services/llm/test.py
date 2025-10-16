"""
GDPR Chatbot with RAG (Retrieval-Augmented Generation)
Combines organization-specific data with general GDPR knowledge
"""


from langchain_huggingface import HuggingFacePipeline
from langchain_community.vectorstores import FAISS
from langchain_huggingface import HuggingFaceEmbeddings
from langchain.text_splitter import RecursiveCharacterTextSplitter
from langchain.chains import ConversationalRetrievalChain
from langchain.memory import ConversationBufferMemory
from langchain.prompts import PromptTemplate
from transformers import AutoTokenizer, AutoModelForCausalLM, pipeline
from flask import Flask, request, jsonify
from flask_cors import CORS
import torch
import os
from typing import List, Dict
import mysql.connector
from datetime import datetime

# ============================================================================
# CONFIGURATION
# ============================================================================
print(torch.__version__)
# HuggingFace API token - must be set as environment variable
api_token = os.getenv("HUGGINGFACE_TOKEN")
if not api_token:
    raise ValueError("HUGGINGFACE_TOKEN environment variable is required. Please set it before running.")

# Database configuration (adjusted for MySQL gdpr database)
DB_CONFIG = {
    "host": os.getenv("DB_HOST", "127.0.0.1"),
    "port": int(os.getenv("DB_PORT", "3306")),
    "database": os.getenv("DB_NAME", "gdpr"),
    "user": os.getenv("DB_USER", "root"),
    "password": os.getenv("DB_PASSWORD", "")
}

# ============================================================================
# LLM SETUP
# ============================================================================

print("Loading LLM model...")
# Using smaller 2B model instead of 8B for faster inference on 8GB GPU
# meta-llama/Meta-Llama-3-8B-Instruct is the old one which did not
# work on setup of 3060 8GB Ti, 16GB RAM, AMD Ryzen 7 8-core 3.60GHz
model_id = "microsoft/Phi-3-mini-4k-instruct"  

tokenizer = AutoTokenizer.from_pretrained(model_id, token=api_token)
model = AutoModelForCausalLM.from_pretrained(
    model_id,
    token=api_token,
    dtype=torch.float16,
    device_map="auto",
    low_cpu_mem_usage=True,
    max_memory={0: "7GB"}  # Limit GPU memory to prevent CPU offloading
)

pipe = pipeline(
    "text-generation",
    model=model,
    tokenizer=tokenizer,
    max_new_tokens=250,  # Max amount of sentences produced in output
    temperature=0.7,
    top_p=0.95,
    repetition_penalty=1.15,
    do_sample=True,
    num_beams=1,  # Faster generation with greedy decoding
    pad_token_id=tokenizer.eos_token_id # Ensure proper stopping
)

llm = HuggingFacePipeline(pipeline=pipe)
print("Model loaded successfully!")

# ============================================================================
# GDPR KNOWLEDGE BASE
# ============================================================================

GDPR_KNOWLEDGE_BASE = [
    """
    GDPR (General Data Protection Regulation) came into effect on May 25, 2018.
    GDPR is the EU's data protection regulation that governs how personal data may be processed.
    It applies to all organizations that process personal data of EU citizens.
    """,

    """
    Sanctions under GDPR:
    - Minor violations: Fines up to 10 million euros or 2% of global annual turnover
    - Serious violations: Fines up to 20 million euros or 4% of global annual turnover
    - Whichever amount is higher applies
    """,

    """
    The six legal bases for processing personal data under GDPR:
    1. Consent - the individual has given their consent
    2. Contract - necessary to fulfill a contract
    3. Legal obligation - required to comply with legal requirements
    4. Vital interests - to protect vital interests
    5. Public task - perform a task in the public interest
    6. Legitimate interests - for legitimate interests
    """,

    """
    Data subject rights under GDPR:
    - Right of access (Article 15) - obtain information about processing
    - Right to rectification (Article 16) - correct inaccurate data
    - Right to erasure (Article 17) - the right to be forgotten
    - Right to restriction (Article 18) - restrict processing
    - Right to data portability (Article 20) - obtain their data
    - Right to object (Article 21) - object to processing
    - Right not to be subject to automated decision-making (Article 22)
    """,

    """
    Personal data breaches under GDPR:
    - Must be reported to supervisory authority within 72 hours
    - If there is high risk to individuals, they must also be informed
    - Documentation of all incidents must be maintained
    """,

    """
    Data Protection Officer (DPO) required when:
    - Public authority or body
    - Core activities require regular large-scale monitoring
    - Large-scale processing of sensitive personal data or criminal data
    """,

    """
    Privacy by Design and Privacy by Default (Article 25):
    - Data protection must be built into systems and processes from the start
    - Default settings should be most privacy-friendly
    - Technical and organizational measures required
    """,

    """
    Data Protection Impact Assessment (DPIA) required when:
    - New technology is used
    - Processing poses high risk to individuals' rights
    - Systematic and comprehensive profiling
    - Large-scale processing of sensitive data
    - Systematic monitoring of publicly accessible areas
    """
]

# ============================================================================
# DATABASE FUNCTIONS
# ============================================================================

def get_organization_data(organization_id: int) -> List[str]:
    """
    Retrieves organization-specific data from the database.
    Adjusted for MySQL gdpr database structure.
    """
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        documents = []

        # Fetch organization info (adjusted for gdpr database schema)
        cursor.execute("""
            SELECT name, number, employees, phone, email, website
            FROM organisations
            WHERE id = %s
        """, (organization_id,))
        org_data = cursor.fetchone()
        if org_data:
            documents.append(
                f"Organization: {org_data[0]}. Number: {org_data[1]}. "
                f"Employees: {org_data[2]}. Contact: {org_data[3]}, {org_data[4]}"
            )

        # Fetch organisation statements
        cursor.execute("""
            SELECT s.name, s.description
            FROM statements s
            JOIN organisation_statement os ON s.id = os.statement_id
            WHERE os.organisation_id = %s
            LIMIT 20
        """, (organization_id,))
        for statement in cursor.fetchall():
            documents.append(f"Statement '{statement[0]}': {statement[1]}")

        # Fetch risks associated with the organization
        cursor.execute("""
            SELECT name, description, probability, impact
            FROM risks
            WHERE organisation_id = %s
            ORDER BY created_at DESC
            LIMIT 10
        """, (organization_id,))
        for risk in cursor.fetchall():
            documents.append(
                f"Risk: {risk[0]} - {risk[1]}. "
                f"Probability: {risk[2]}, Impact: {risk[3]}"
            )

        # Fetch tasks for the organization
        cursor.execute("""
            SELECT name, description, status
            FROM tasks
            WHERE organisation_id = %s
            ORDER BY created_at DESC
            LIMIT 10
        """, (organization_id,))
        for task in cursor.fetchall():
            documents.append(
                f"Task '{task[0]}': {task[1]}. Status: {task[2]}"
            )

        cursor.close()
        conn.close()

        return documents

    except Exception as e:
        print(f"Database error: {e}")
        return []

# ============================================================================
# RAG SYSTEM
# ============================================================================

class GDPRChatbot:
    def __init__(self, llm):
        self.llm = llm
        self.embeddings = HuggingFaceEmbeddings(
            model_name="sentence-transformers/all-MiniLM-L6-v2"
        )
        self.vectorstore = None
        self.qa_chain = None
        self.sessions = {}  # Session-based memory management

        # Custom prompt for GDPR context to give the model an identity and thus giving more
        # focused responses within its specified domain.
        # Context ensures the model uses RAG-retrieved documents which prevents hallucination
        # and is crititcal for accuracy in legal/compliance domains.
        self.prompt_template = """You are a GDPR expert. Answer the question using only the context provided below. Be concise and practical.

        Context:
        {context}

        Previous conversation:
        {chat_history}

        Question: {question}

        Answer:"""

    def initialize_for_organization(self, organization_id: int):
        """Initializes the chatbot with data for a specific organization"""
        print(f"Loading data for organization {organization_id}...")

        # Combine GDPR knowledge with organization data
        org_documents = get_organization_data(organization_id)
        all_documents = GDPR_KNOWLEDGE_BASE + org_documents

        # Create vectorstore
        text_splitter = RecursiveCharacterTextSplitter(
            chunk_size=100,
            chunk_overlap=50
        )
        texts = text_splitter.create_documents(all_documents)
        self.vectorstore = FAISS.from_documents(texts, self.embeddings)

        print(f"Loaded {len(all_documents)} documents!")

    def create_session(self, session_id: str, organization_id: int):
        """Creates a new session with memory management"""
        if session_id not in self.sessions:
            self.initialize_for_organization(organization_id)

            memory = ConversationBufferMemory(
                memory_key="chat_history",
                return_messages=True,
                output_key="answer"
            )

            prompt = PromptTemplate(
                template=self.prompt_template,
                input_variables=["context", "chat_history", "question"]
            )

            self.qa_chain = ConversationalRetrievalChain.from_llm(
                llm=self.llm,
                retriever=self.vectorstore.as_retriever(search_kwargs={"k": 5}),
                memory=memory,
                return_source_documents=True,
                combine_docs_chain_kwargs={"prompt": prompt}
            )

            self.sessions[session_id] = {
                "chain": self.qa_chain,
                "organization_id": organization_id,
                "created_at": datetime.now()
            }

    def chat(self, session_id: str, question: str) -> Dict:
        """Sends a question and receives an answer"""
        if session_id not in self.sessions:
            return {"error": "Session not found. Please create a session first."}

        chain = self.sessions[session_id]["chain"]
        result = chain.invoke({"question": question})

        # Clean up the response - remove the prompt template if it appears in output
        answer = result["answer"]

        # If the model echoed the prompt, extract only the part after "Answer:"
        if "Answer:" in answer:
            # Split by "Answer:" and take the last part (the actual answer)
            answer = answer.split("Answer:")[-1].strip()

        # Remove any remaining prompt artifacts
        answer = answer.replace("Context:", "").replace("Question:", "").replace("Previous conversation:", "")
        answer = answer.strip()

        return {
            "answer": answer,
            "sources": [doc.page_content[:200] for doc in result.get("source_documents", [])]
        }

# ============================================================================
# FLASK API
# ============================================================================

app = Flask(__name__)
CORS(app)  # Enable CORS for frontend

chatbot = GDPRChatbot(llm)

@app.route("/api/chat/init", methods=["POST"])
def initialize_chat():
    """
    Initializes a new chat session
    Body: { "session_id": "unique-id", "organization_id": 1 }
    """
    data = request.json
    session_id = data.get("session_id")
    organization_id = data.get("organization_id")

    if not session_id or not organization_id:
        return jsonify({"error": "session_id and organization_id required"}), 400

    try:
        chatbot.create_session(session_id, organization_id)
        return jsonify({
            "status": "success",
            "message": f"Session initialized for organization {organization_id}"
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route("/api/chat/message", methods=["POST"])
def send_message():
    """
    Sends a message to the chatbot
    Body: { "session_id": "unique-id", "message": "What is GDPR?" }
    """
    data = request.json
    session_id = data.get("session_id")
    message = data.get("message")

    if not session_id or not message:
        return jsonify({"error": "session_id and message required"}), 400

    try:
        result = chatbot.chat(session_id, message)
        return jsonify(result)
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route("/api/chat/sessions", methods=["GET"])
def list_sessions():
    """Lists all active sessions"""
    sessions_info = {}
    for sid, data in chatbot.sessions.items():
        sessions_info[sid] = {
            "organization_id": data["organization_id"],
            "created_at": data["created_at"].isoformat()
        }
    return jsonify(sessions_info)

@app.route("/health", methods=["GET"])
def health():
    """Health check endpoint"""
    return jsonify({"status": "healthy"})

# ============================================================================
# MAIN
# ============================================================================

if __name__ == "__main__":
    print("\n" + "="*70)
    print("GDPR CHATBOT WITH RAG")
    print("="*70)
    print("\nAPI Endpoints:")
    print("  POST /api/chat/init     - Initialize new session")
    print("  POST /api/chat/message  - Send message")
    print("  GET  /api/chat/sessions - List sessions")
    print("  GET  /health            - Health check")
    print("\n" + "="*70 + "\n")

    # Start Flask server
    app.run(host="0.0.0.0", port=5001, debug=True)
