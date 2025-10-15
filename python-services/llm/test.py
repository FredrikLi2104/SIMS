"""
GDPR Chatbot with RAG (Retrieval-Augmented Generation)
Combines organization-specific data with general GDPR knowledge
"""

import sys
import io

# Fix Windows console encoding for emoji support
if sys.platform == 'win32':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
    sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

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
import psycopg2
from datetime import datetime

# ============================================================================
# CONFIGURATION
# ============================================================================

# HuggingFace API token
api_token = os.getenv("HUGGINGFACE_TOKEN", "TOKEN_NOT_SET")

# Database configuration (adjust to your database)
DB_CONFIG = {
    "host": os.getenv("DB_HOST", "localhost"),
    "port": os.getenv("DB_PORT", "5432"),
    "database": os.getenv("DB_NAME", "sims"),
    "user": os.getenv("DB_USER", "postgres"),
    "password": os.getenv("DB_PASSWORD", "")
}

# ============================================================================
# LLM SETUP
# ============================================================================

print("🚀 Loading LLM model...")
model_id = "meta-llama/Meta-Llama-3-8B-Instruct"

tokenizer = AutoTokenizer.from_pretrained(model_id, token=api_token)
model = AutoModelForCausalLM.from_pretrained(
    model_id,
    token=api_token,
    torch_dtype=torch.float16,
    device_map="auto",
    low_cpu_mem_usage=True
)

pipe = pipeline(
    "text-generation",
    model=model,
    tokenizer=tokenizer,
    max_new_tokens=512,
    temperature=0.7,
    top_p=0.95,
    repetition_penalty=1.15
)

llm = HuggingFacePipeline(pipeline=pipe)
print("✅ Model loaded successfully!")

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
    Adjust SQL queries according to your database structure.
    """
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cursor = conn.cursor()

        documents = []

        # Fetch organization info
        cursor.execute("""
            SELECT name, description, industry
            FROM organizations
            WHERE id = %s
        """, (organization_id,))
        org_data = cursor.fetchone()
        if org_data:
            documents.append(f"Organization: {org_data[0]}. Description: {org_data[1]}. Industry: {org_data[2]}")

        # Fetch data protection policies
        cursor.execute("""
            SELECT title, content, created_at
            FROM policies
            WHERE organization_id = %s
            ORDER BY created_at DESC
        """, (organization_id,))
        for policy in cursor.fetchall():
            documents.append(f"Policy '{policy[0]}': {policy[1]}")

        # Fetch incidents
        cursor.execute("""
            SELECT incident_type, description, severity, status
            FROM incidents
            WHERE organization_id = %s
            ORDER BY created_at DESC
            LIMIT 10
        """, (organization_id,))
        for incident in cursor.fetchall():
            documents.append(
                f"Incident ({incident[0]}): {incident[1]}. "
                f"Severity: {incident[2]}, Status: {incident[3]}"
            )

        # Fetch processing activities
        cursor.execute("""
            SELECT activity_name, purpose, legal_basis, data_categories
            FROM processing_activities
            WHERE organization_id = %s
        """, (organization_id,))
        for activity in cursor.fetchall():
            documents.append(
                f"Processing activity '{activity[0]}': Purpose: {activity[1]}, "
                f"Legal basis: {activity[2]}, Data categories: {activity[3]}"
            )

        cursor.close()
        conn.close()

        return documents

    except Exception as e:
        print(f"❌ Database error: {e}")
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

        # Custom prompt for GDPR context
        self.prompt_template = """
        You are a data protection and GDPR expert helping organizations understand and comply with data protection regulations.

        Use the following context to answer the question. The context contains both general GDPR knowledge
        and organization-specific information.

        If you don't know the answer, say that you don't know. Don't try to make up information.
        Provide concrete and practical answers with references to relevant GDPR articles when appropriate.

        Context: {context}

        Chat history: {chat_history}

        Question: {question}

        Answer:"""

    def initialize_for_organization(self, organization_id: int):
        """Initializes the chatbot with data for a specific organization"""
        print(f"📚 Loading data for organization {organization_id}...")

        # Combine GDPR knowledge with organization data
        org_documents = get_organization_data(organization_id)
        all_documents = GDPR_KNOWLEDGE_BASE + org_documents

        # Create vectorstore
        text_splitter = RecursiveCharacterTextSplitter(
            chunk_size=500,
            chunk_overlap=50
        )
        texts = text_splitter.create_documents(all_documents)
        self.vectorstore = FAISS.from_documents(texts, self.embeddings)

        print(f"✅ Loaded {len(all_documents)} documents!")

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
        result = chain({"question": question})

        return {
            "answer": result["answer"],
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
    print("🤖 GDPR CHATBOT WITH RAG")
    print("="*70)
    print("\n📡 API Endpoints:")
    print("  POST /api/chat/init     - Initialize new session")
    print("  POST /api/chat/message  - Send message")
    print("  GET  /api/chat/sessions - List sessions")
    print("  GET  /health            - Health check")
    print("\n" + "="*70 + "\n")

    # Start Flask server
    app.run(host="0.0.0.0", port=5001, debug=True)
