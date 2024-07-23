import tkinter as tk
from tkinter import messagebox, scrolledtext
import sqlite3

# Configuration de la base de données
def init_db():
    conn = sqlite3.connect('incidents.db')
    c = conn.cursor()
    c.execute('''CREATE TABLE IF NOT EXISTS incidents (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    room TEXT NOT NULL,
                    issue TEXT NOT NULL)''')
    conn.commit()
    conn.close()

# Fonction pour ajouter un incident
def add_incident():
    room = room_var.get()
    issue = issue_entry.get()
    if room and issue:
        conn = sqlite3.connect('incidents.db')
        c = conn.cursor()
        c.execute("INSERT INTO incidents (room, issue) VALUES (?, ?)", (room, issue))
        conn.commit()
        conn.close()
        messagebox.showinfo("Succès", "L'incident a été signalé avec succès")
        issue_entry.delete(0, tk.END)
        update_incident_list()
    else:
        messagebox.showwarning("Attention", "Veuillez remplir tous les champs")

# Fonction pour mettre à jour la liste des incidents
def update_incident_list():
    incident_list.delete(0, tk.END)
    conn = sqlite3.connect('incidents.db')
    c = conn.cursor()
    c.execute("SELECT room, issue FROM incidents")
    rows = c.fetchall()
    for row in rows:
        incident_list.insert(tk.END, f"Salle: {row[0]}, Problème: {row[1]}")
    conn.close()

# Fonction pour afficher l'interface employé
def show_employee_interface():
    login_frame.pack_forget()
    employee_frame.pack(fill="both", expand=True)
    update_incident_list()

# Fonction pour afficher l'interface administrateur
def show_admin_interface():
    login_frame.pack_forget()
    admin_frame.pack(fill="both", expand=True)
    update_incident_list()

# Fonction pour afficher le contenu de la base de données dans une nouvelle fenêtre
def view_db():
    conn = sqlite3.connect('incidents.db')
    c = conn.cursor()
    c.execute("SELECT * FROM incidents")
    rows = c.fetchall()
    conn.close()
    
    db_window = tk.Toplevel(root)
    db_window.title("Contenu de la Base de Données")
    db_text = scrolledtext.ScrolledText(db_window, width=80, height=20)
    db_text.pack(pady=10, padx=10)
    
    for row in rows:
        db_text.insert(tk.END, f"ID: {row[0]}, Salle: {row[1]}, Problème: {row[2]}\n")

# Fonction pour revenir à la page de connexion
def back_to_login():
    employee_frame.pack_forget()
    admin_frame.pack_forget()
    login_frame.pack(fill="both", expand=True)

# Initialiser la base de données
init_db()

# Créer la fenêtre principale
root = tk.Tk()
root.title("Application de Communication des Incidents")
root.geometry("600x500")

# Frame de connexion
login_frame = tk.Frame(root)
login_frame.pack(fill="both", expand=True)

tk.Label(login_frame, text="Choisissez votre type de connexion:", font=("Helvetica", 16)).pack(pady=20)
tk.Button(login_frame, text="Connexion Employé", command=show_employee_interface, width=20, height=2).pack(pady=10)
tk.Button(login_frame, text="Connexion Administrateur", command=show_admin_interface, width=20, height=2).pack(pady=10)

# Frame employé
employee_frame = tk.Frame(root)

tk.Label(employee_frame, text="Sélectionnez la salle:", font=("Helvetica", 12)).pack(pady=5)
room_var = tk.StringVar()
room_entry = tk.Entry(employee_frame, textvariable=room_var, width=30)
room_entry.pack(pady=5)

tk.Label(employee_frame, text="Description de l'incident:", font=("Helvetica", 12)).pack(pady=5)
issue_entry = tk.Entry(employee_frame, width=50)
issue_entry.pack(pady=5)

tk.Button(employee_frame, text="Signaler l'incident", command=add_incident, width=20).pack(pady=10)

tk.Label(employee_frame, text="Liste des incidents signalés:", font=("Helvetica", 12)).pack(pady=5)
incident_list = tk.Listbox(employee_frame, width=70, height=15)
incident_list.pack(pady=5)

tk.Button(employee_frame, text="Retour", command=back_to_login, width=20).pack(pady=10)

# Frame administrateur
admin_frame = tk.Frame(root)
tk.Label(admin_frame, text="Interface Administrateur - Liste des incidents:", font=("Helvetica", 16)).pack(pady=20)
admin_incident_list = tk.Listbox(admin_frame, width=70, height=20)
admin_incident_list.pack(pady=5)

tk.Button(admin_frame, text="Voir la base de données", command=view_db, width=20).pack(pady=10)
tk.Button(admin_frame, text="Retour", command=back_to_login, width=20).pack(pady=10)

# Mettre à jour la liste des incidents au démarrage
update_incident_list()

# Boucle principale
root.mainloop()
