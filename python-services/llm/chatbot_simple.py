"""
GDPR Chatbot - Lightweight Version
Uses OpenAI API or simpler models instead of local LLM
"""

import sys
import io

# Fix Windows console encoding
if sys.platform == 'win32':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
    sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

from flask import Flask, request, jsonify
from flask_cors import CORS
import os
from typing import List, Dict
from datetime import datetime

# ============================================================================
# CONFIGURATION
# ============================================================================

# ============================================================================
# GDPR KNOWLEDGE BASE
# ============================================================================

GDPR_KNOWLEDGE_BASE = {
    "gdpr_basics": """
    GDPR (General Data Protection Regulation) came into effect on May 25, 2018.
    It is the EU's data protection regulation that governs how personal data may be processed.
    It applies to all organizations that process personal data of EU citizens.
    """,

    "sanctions": """
    Sanctions under GDPR:
    - Minor violations: Fines up to €10 million or 2% of global annual turnover (whichever is higher)
    - Serious violations: Fines up to €20 million or 4% of global annual turnover (whichever is higher)
    """,

    "legal_bases": """
    The six legal bases for processing personal data under GDPR:
    1. Consent - the individual has given their consent
    2. Contract - necessary to fulfill a contract
    3. Legal obligation - required to comply with legal requirements
    4. Vital interests - to protect vital interests
    5. Public task - perform a task in the public interest
    6. Legitimate interests - for legitimate interests
    """,

    "data_subject_rights": """
    Data subject rights under GDPR:
    - Right of access (Article 15) - obtain information about processing
    - Right to rectification (Article 16) - correct inaccurate data
    - Right to erasure (Article 17) - the right to be forgotten
    - Right to restriction (Article 18) - restrict processing
    - Right to data portability (Article 20) - obtain their data
    - Right to object (Article 21) - object to processing
    - Right not to be subject to automated decision-making (Article 22)
    """,

    "data_breaches": """
    Personal data breaches under GDPR:
    - Must be reported to supervisory authority within 72 hours
    - If there is high risk to individuals, they must also be informed
    - Documentation of all incidents must be maintained
    """,

    "dpo": """
    Data Protection Officer (DPO) required when:
    - Public authority or body
    - Core activities require regular large-scale monitoring
    - Large-scale processing of sensitive personal data or criminal data
    """,

    "privacy_by_design": """
    Privacy by Design and Privacy by Default (Article 25):
    - Data protection must be built into systems and processes from the start
    - Default settings should be most privacy-friendly
    - Technical and organizational measures required
    """,

    "dpia": """
    Data Protection Impact Assessment (DPIA) required when:
    - New technology is used
    - Processing poses high risk to individuals' rights
    - Systematic and comprehensive profiling
    - Large-scale processing of sensitive data
    - Systematic monitoring of publicly accessible areas
    """
}

# ============================================================================
# SIMPLE CHATBOT (KEYWORD-BASED)
# ============================================================================

class GDPRChatbotSimple:
    def __init__(self):
        self.sessions = {}
        self.knowledge = GDPR_KNOWLEDGE_BASE

    def create_session(self, session_id: str, organization_id: int):
        """Creates a new session"""
        if session_id not in self.sessions:
            self.sessions[session_id] = {
                "organization_id": organization_id,
                "created_at": datetime.now(),
                "history": []
            }
            print(f"✅ Session {session_id} created for organization {organization_id}")

    def find_relevant_info(self, question: str) -> str:
        """Find relevant GDPR information based on keywords"""
        question_lower = question.lower()

        # Check for specific topics
        if any(word in question_lower for word in ['sanction', 'fine', 'penalty', 'punishment']):
            return self.knowledge['sanctions']

        elif any(word in question_lower for word in ['legal basis', 'legal ground', 'lawful basis']):
            return self.knowledge['legal_bases']

        elif any(word in question_lower for word in ['rights', 'data subject', 'individual rights']):
            return self.knowledge['data_subject_rights']

        elif any(word in question_lower for word in ['breach', 'incident', 'leak', 'violation']):
            return self.knowledge['data_breaches']

        elif any(word in question_lower for word in ['dpo', 'data protection officer']):
            return self.knowledge['dpo']

        elif any(word in question_lower for word in ['privacy by design', 'privacy by default']):
            return self.knowledge['privacy_by_design']

        elif any(word in question_lower for word in ['dpia', 'impact assessment']):
            return self.knowledge['dpia']

        elif any(word in question_lower for word in ['what is gdpr', 'about gdpr', 'explain gdpr']):
            return self.knowledge['gdpr_basics']

        # Default response
        return """
        I can help you with information about:
        - GDPR sanctions and fines
        - Legal bases for data processing
        - Data subject rights
        - Data breach requirements
        - Data Protection Officers (DPO)
        - Privacy by Design and Default
        - Data Protection Impact Assessments (DPIA)

        What would you like to know more about?
        """

    def chat(self, session_id: str, question: str) -> Dict:
        """Process a chat message"""
        if session_id not in self.sessions:
            return {"error": "Session not found. Please create a session first."}

        # Find relevant information
        answer = self.find_relevant_info(question)

        # Store in history
        self.sessions[session_id]["history"].append({
            "question": question,
            "answer": answer,
            "timestamp": datetime.now()
        })

        return {
            "answer": answer.strip(),
            "sources": ["GDPR Knowledge Base"]
        }

# ============================================================================
# FLASK API
# ============================================================================

app = Flask(__name__)
CORS(app)

chatbot = GDPRChatbotSimple()

@app.route("/api/chat/init", methods=["POST"])
def initialize_chat():
    """Initialize a new chat session"""
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
    """Send a message to the chatbot"""
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
    """List all active sessions"""
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
    print("🤖 GDPR CHATBOT (Lightweight Version)")
    print("="*70)
    print("\n📡 API Endpoints:")
    print("  POST /api/chat/init     - Initialize new session")
    print("  POST /api/chat/message  - Send message")
    print("  GET  /api/chat/sessions - List sessions")
    print("  GET  /health            - Health check")
    print("\n" + "="*70 + "\n")

    # Start Flask server
    app.run(host="0.0.0.0", port=5001, debug=False)